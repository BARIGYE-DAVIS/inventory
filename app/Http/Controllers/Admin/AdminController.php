<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    protected string $twoFactorRoute = 'admin.auth.twofactor. show';

    // ========================================
    // SETUP (First Admin)
    // ========================================
    public function showSetup()
    {
        if (Admin::exists()) {
            return redirect()->route('admin.login');
        }
        return view('admin.auth.setup');
    }

    public function storeSetup(Request $request)
    {
        if (Admin::exists()) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'is_superadmin' => true,
            'two_factor_enabled' => true,
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->route($this->twoFactorRoute)
            ->with('success', 'Admin account created.  Please verify with 2FA.');
    }

    // ========================================
    // LOGIN
    // ========================================
    public function showLogin()
    {
        if (! Admin::exists()) {
            return redirect()->route('admin.setup. show');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $admin = Admin::where('email', $data['email'])->first();
        
        if ($admin && ! $admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This admin account is deactivated.'],
            ]);
        }

        if (! Auth::guard('admin')->attempt(
            ['email' => $data['email'], 'password' => $data['password']],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        $request->session()->regenerate();

        $authAdmin = Auth::guard('admin')->user();
        if ($authAdmin) {
            $authAdmin->update(['last_login_at' => now()]);
        }

        return redirect()->route($this->twoFactorRoute)
            ->with('success', 'Verification code sent to your email.');
    }

    // ========================================
    // LOGOUT
    // ========================================
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Logged out successfully.');
    }

    // ========================================
    // DASHBOARD
    // ========================================
    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();
        
        $stats = [
            'total_users' => User::count(),
            'active_users' => User:: where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
        ];

        return view('admin.dashboard', compact('admin', 'stats'));
    }

    // ========================================
    // USERS MANAGEMENT
    // ========================================
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(20);

        return view('admin. users. index', compact('users'));
    }

    public function toggleUserActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 
            $user->is_active ? 'User activated.' : 'User deactivated.');
    }

    public function updateUserEmail(Request $request, User $user)
    {
        $data = $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update(['email' => $data['email']]);

        return back()->with('success', 'User email updated.');
    }

    // ========================================
    // PROFILE
    // ========================================
    public function editProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile. edit', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('admins')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        
        if (! empty($data['password'])) {
            $admin->password = Hash:: make($data['password']);
        }
        
        $admin->save();

        return back()->with('success', 'Profile updated.');
    }
}