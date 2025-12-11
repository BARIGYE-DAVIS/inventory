<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin; // Dedicated admin accounts (NOT business users)
use App\Models\User;  // Business users managed by admins
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Change this to your existing 2FA verification route name.
     * Example: 'auth.twofactor.show' or 'twofactor.verify.show'
     */
    protected string $twoFactorRoute = 'admin.auth.twofactor.show';

    /**
     * STEP 1: First-time setup (only if no admin exists).
     * GET /admin/setup
     */
    public function showSetup()
    {
        if (Admin::query()->exists()) {
            return redirect()->route('admin.login');
        }
        return view('admin.auth.setup');
    }

    /**
     * POST /admin/setup
     * Creates the first Admin, logs in with admin guard, and hands off to existing 2FA.
     */
    public function storeSetup(Request $request)
    {
        if (Admin::query()->exists()) {
            return redirect()->route('admin.login');
        }

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = Admin::create([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'password'           => Hash::make($data['password']),
            'is_active'          => true,
            'is_superadmin'      => true,
            'two_factor_enabled' => true,
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->route($this->twoFactorRoute)
            ->with('success', 'Admin created. A verification code has been sent to your email.');
    }

    /**
     * STEP 2: Admin login page.
     * GET /admin/login
     */
    public function showLogin()
    {
        if (!Admin::query()->exists()) {
            return redirect()->route('admin.setup.show');
        }
        return view('admin.auth.login');
    }

    /**
     * POST /admin/login
     * Attempts login via admin guard and redirects to existing 2FA verification.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        $admin = Admin::where('email', $data['email'])->first();
        if ($admin && !$admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This admin account is deactivated.'],
            ]);
        }

        if (!Auth::guard('admin')->attempt(
            ['email' => $data['email'], 'password' => $data['password']],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $request->session()->regenerate();

        $authAdmin = Auth::guard('admin')->user();
        if ($authAdmin instanceof \App\Models\Admin) {
            $authAdmin->last_login_at = now();
            $authAdmin->save();
        }

        return redirect()->route($this->twoFactorRoute)
            ->with('success', 'Verification code sent to your email.');
    }

    /**
     * POST /admin/logout
     * Logs out the admin guard session.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out.');
    }

    /**
     * STEP 3: Admin Dashboard (stats + quick overviews).
     * GET /admin
     * Requires auth:admin (+ your existing 2FA verified middleware).
     */
public function dashboard(Request $request)
{
    $admin = Auth::guard('admin')->user();
    abort_unless($admin && $admin->is_active, 403);

    // Summary stats
    $stats = [
        'users_total'    => User::count(),
        'users_active'   => User::where('is_active', true)->count(),
        'users_inactive' => User::where('is_active', false)->count(),
        'users_online'   => User::whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->count(),
    ];

    // Usage frequency (DAU/WAU/MAU) from last_activity_at
    $usage = [
        'dau' => User::whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subDay())
            ->distinct('id')->count('id'),
        'wau' => User::whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subDays(7))
            ->distinct('id')->count('id'),
        'mau' => User::whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subDays(30))
            ->distinct('id')->count('id'),
    ];

    // DAU trend for the last 14 days
    $days = collect(range(0, 13))->map(function ($i) {
        $start = now()->subDays($i)->startOfDay();
        $end   = $start->copy()->addDay();
        $count = User::whereNotNull('last_activity_at')
            ->whereBetween('last_activity_at', [$start, $end])
            ->distinct('id')->count('id');
        return ['date' => $start->toDateString(), 'dau' => $count];
    })->reverse()->values();

    // Optional “recency” buckets to see how often users are active
    $usageBuckets = [
        'last_24h'   => User::whereNotNull('last_activity_at')->where('last_activity_at', '>=', now()->subDay())->count(),
        'last_7d'    => User::whereNotNull('last_activity_at')->where('last_activity_at', '>=', now()->subDays(7))->count(),
        'last_30d'   => User::whereNotNull('last_activity_at')->where('last_activity_at', '>=', now()->subDays(30))->count(),
        'over_30d'   => User::whereNotNull('last_activity_at')->where('last_activity_at', '<',  now()->subDays(30))->count(),
        'never_seen' => User::whereNull('last_activity_at')->count(),
    ];

    // Recent activity and plans
    $recent = User::orderByDesc('last_activity_at')->limit(10)->get();
    $plans = User::selectRaw('COALESCE(plan, "none") as plan, COUNT(*) as total')->groupBy('plan')->get();

    return view('admin.dashboard', compact('stats', 'usage', 'days', 'usageBuckets', 'recent', 'plans'));
}
    /**
     * Users management index (filters + pagination).
     * GET /admin/users
     */
    public function users(Request $request)
    {
        $this->ensureAdminActive();

        $query = User::query();

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * PATCH /admin/users/{user}/email
     */
    public function updateUserEmail(Request $request, User $user)
    {
        $this->ensureAdminActive();

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->email = $data['email'];
        $user->save();

        return back()->with('success', 'User email updated.');
    }

    /**
     * PATCH /admin/users/{user}/toggle
     * Activate/Deactivate a user.
     */
    public function toggleUserActive(User $user)
    {
        $this->ensureAdminActive();

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', $user->is_active ? 'User activated.' : 'User deactivated.');
    }

    /**
     * PATCH /admin/users/{user}/plan
     */
    public function updateUserPlan(Request $request, User $user)
    {
        $this->ensureAdminActive();

        $data = $request->validate([
            'plan'            => ['required', 'string', 'in:free,pro,business'],
            'plan_expires_at' => ['nullable', 'date'],
        ]);

        $user->plan = $data['plan'];
        $user->plan_expires_at = $data['plan_expires_at'] ?? null;
        $user->save();

        return back()->with('success', 'User plan updated.');
    }

    /**
     * Admin profile edit page.
     * GET /admin/profile
     */
    public function editProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * PATCH /admin/profile
     * Update admin's own profile (name/email/password).
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!($admin instanceof \App\Models\Admin)) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }
        $admin->save();

        return back()->with('success', 'Profile updated.');
    }

    /**
     * Helper to guard admin actions.
     */
    protected function ensureAdminActive(): void
    {
        $admin = Auth::guard('admin')->user();
        abort_unless($admin && $admin->is_active, 403);
    }
}