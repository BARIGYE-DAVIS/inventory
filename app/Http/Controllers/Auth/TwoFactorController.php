<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TwoFactorController extends Controller
{
    /**
     * Determine which guard we're using
     */
    private function determineGuard(): string
    {
        if (Auth::guard('admin')->check()) {
            return 'admin';
        }
        return 'web'; // business users
    }

    /**
     * Show 2FA verification page
     */
    public function show(Request $request)
    {
        $guard = $this->determineGuard();

        if (! Auth::guard($guard)->check()) {
            return redirect()->route($guard === 'admin' ? 'admin.login' : 'login');
        }

        if (session('two_factor_verified') === true) {
            return $this->redirectAfterVerification($guard);
        }

        $this->ensureCodeExists($guard);

        $user = Auth::guard($guard)->user();

        return view('auth.twofactor', [
            'user' => $user,
            'guard' => $guard,
            'expires_at' => $user->two_factor_expires_at,
        ]);
    }

    /**
     * Verify 2FA code
     */
    public function verify(Request $request)
    {
        $guard = $this->determineGuard();

        if (!Auth::guard($guard)->check()) {
            return redirect()->route($guard === 'admin' ? 'admin.login' : 'login');
        }

        $data = $request->validate([
            'code' => ['required', 'digits: 6'],
        ]);

        $user = Auth::guard($guard)->user();

        if (!$user->two_factor_code || ! $user->two_factor_expires_at) {
            return back()->withErrors(['code' => 'No verification code found.  Please resend. ']);
        }

        if (now()->greaterThan(Carbon::parse($user->two_factor_expires_at))) {
            return back()->withErrors(['code' => 'Code expired. Please request a new one.']);
        }

        if ($data['code'] !== $user->two_factor_code) {
            return back()->withErrors(['code' => 'Invalid code. Try again.'])->withInput();
        }



        // Mark as verified
        session(['two_factor_verified' => true]);

        // Clear 2FA code
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        return $this->redirectAfterVerification($guard)
            ->with('success', 'Verification successful! ');
    }

    /**
     * Resend 2FA code
     */
    public function resend(Request $request)
    {
        $guard = $this->determineGuard();

        if (!Auth::guard($guard)->check()) {
            return redirect()->route($guard === 'admin' ? 'admin. login' : 'login');
        }

        // Rate limit:  wait 30 seconds between resends
        $last = session('two_factor_last_resent_at');
        if ($last && now()->diffInSeconds(Carbon::parse($last)) < 30) {
            return back()->withErrors(['code' => 'Please wait 30 seconds before requesting another code.']);
        }

        $this->generateAndSendCode($guard);
        session(['two_factor_last_resent_at' => now()->toDateTimeString()]);

        return back()->with('success', 'New verification code sent!');
    }

    /**
     * Generate and send 2FA code
     */
    private function generateAndSendCode(string $guard): void
    {
        $user = Auth::guard($guard)->user();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(3);
        $user->save();

        Mail::to($user->email)->send(new TwoFactorCodeMail($code));
    }

    /**
     * Ensure code exists, generate if needed
     */
    private function ensureCodeExists(string $guard): void
    {
        $user = Auth::guard($guard)->user();

        if (
            !$user->two_factor_code ||
            !$user->two_factor_expires_at ||
            now()->greaterThan(Carbon:: parse($user->two_factor_expires_at))
        ) {
            $this->generateAndSendCode($guard);
        }
    }

    /**
     * Redirect after verification based on guard
     */
    private function redirectAfterVerification(string $guard)
    {
        if ($guard === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Business user - redirect based on role
        $user = Auth::guard('web')->user();
        $role = $user->role->name ??  'owner';

        return match($role) {
            'cashier' => redirect()->route('cashier.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}