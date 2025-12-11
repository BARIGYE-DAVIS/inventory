<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Pass roles to login view
        $roles = Role::all();

        return view('auth.login', compact('roles'));
    }

    /**
     * Handle login request (with 2FA handoff)
     */
    public function login(Request $request)
    {
        // Validate the login credentials
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'role_id'  => ['required', 'exists:roles,id'], // validate selected role
        ]);

        // Fetch user record by email
        $user = \App\Models\User::where('email', $request->email)->first();

        // Block deactivated users
        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact the administrator.'],
            ]);
        }

        // Ensure selected role matches user's role
        if ($user && (int) $user->role_id !== (int) $request->role_id) {
            throw ValidationException::withMessages([
                'role_id' => ['The selected role does not match your account. Please select the correct role.'],
            ]);
        }

        // Attempt authentication
        if (Auth::attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->boolean('remember')
        )) {
            // Regenerate session to prevent fixation
            $request->session()->regenerate();

            // Authenticated user
            $user = Auth::user();

            // Optional: update last login timestamp if column exists
            try {
                $user->update(['last_login_at' => now()]);
            } catch (\Throwable $e) {
                // Column may not exist; ignore
            }

            // 2FA handoff:
            // - Reset the verification flag on each successful login
            // - If 2FA is enabled (default to enabled if column is missing/null), send user to 2FA challenge
            session(['two_factor_verified' => false]);

            $twoFactorEnabled = (int) data_get($user, 'two_factor_enabled', 1) === 1;
            if ($twoFactorEnabled) {
                return redirect()->route('auth.twofactor.show');
            }

            // If 2FA disabled, proceed with role-based redirect
            return $this->redirectBasedOnRole($user)
                ->with('success', "Welcome back, {$user->name}!");
        }

        // If authentication failed
        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole($user)
    {
        $userRole = $user->role->name;

        switch ($userRole) {
            case 'cashier':
                return redirect()->route('cashier.dashboard')
                    ->with('success', "Welcome back, {$user->name}! 💰");

            case 'admin':
                return redirect()->route('dashboard')
                    ->with('success', "Welcome back, {$user->name}! 🛡️");

            case 'manager':
                return redirect()->route('dashboard')
                    ->with('success', "Welcome back, {$user->name}! 📊");

            case 'inventory':
                return redirect()->route('dashboard')
                    ->with('success', "Welcome back, {$user->name}! 📦");

            case 'accountant':
                return redirect()->route('dashboard')
                    ->with('success', "Welcome back, {$user->name}! 💼");

            case 'owner':
            default:
                return redirect()->route('dashboard')
                    ->with('success', "Welcome back, {$user->name}! 👑");
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully! 👋');
    }
}