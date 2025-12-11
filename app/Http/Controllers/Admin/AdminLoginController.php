<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin; // Dedicated Admin model (NOT business users)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * Point this to YOUR existing 2FA verification route.
     * Example: 'auth.twofactor.show' or 'twofactor.show'
     */
    protected string $twoFactorRoute = 'auth.twofactor.show';

    /**
     * GET /admin/login
     * Shows the admin login form. If no admin exists, redirect to setup.
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
     * Uses the 'admin' guard and your existing 2FA flow.
     * After successful login, redirects to your 2FA verification route.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        // Prevent login if deactivated
        $admin = Admin::where('email', $credentials['email'])->first();
        if ($admin && !$admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This admin account is deactivated.'],
            ]);
        }

        // Attempt login with admin guard
        if (!Auth::guard('admin')->attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Rotate session ID
        $request->session()->regenerate();

        // Optional: track last login
        $authAdmin = Auth::guard('admin')->user();
        if ($authAdmin instanceof \App\Models\Admin) {
            $authAdmin->last_login_at = now();
            $authAdmin->save();
        }

        // Hand off to your existing 2FA verification page
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
}