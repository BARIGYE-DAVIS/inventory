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
    public function show()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        if (session('two_factor_verified') === true) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $this->ensureCode();

        return view('auth.twofactor', [
            'user' => Auth::user(),
            'expires_at' => Auth::user()->two_factor_expires_at,
        ]);
    }

    public function verify(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        if (!$user->two_factor_code || !$user->two_factor_expires_at) {
            return back()->withErrors(['code' => 'No verification code found. Please resend a new code.']);
        }

        if (now()->greaterThan(Carbon::parse($user->two_factor_expires_at))) {
            return back()->withErrors(['code' => 'Code expired. Please request a new code.']);
        }

        if ($data['code'] !== $user->two_factor_code) {
            return back()->withErrors(['code' => 'Invalid code. Try again or resend a new code.'])->withInput();
        }

        session(['two_factor_verified' => true]);

        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        return $this->redirectBasedOnRole($user)->with('success', 'Verification successful.');
    }

    public function resend(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $last = session('two_factor_last_resent_at');
        if ($last && now()->diffInSeconds(Carbon::parse($last)) < 30) {
            return back()->withErrors(['code' => 'Please wait a moment before requesting another code.']);
        }

        $this->generateAndSendCode();
        session(['two_factor_last_resent_at' => now()->toDateTimeString()]);

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    private function ensureCode(): void
    {
        $user = Auth::user();
        if (
            !$user->two_factor_code ||
            !$user->two_factor_expires_at ||
            now()->greaterThan(Carbon::parse($user->two_factor_expires_at))
        ) {
            $this->generateAndSendCode();
        }
    }

    private function generateAndSendCode(): void
    {
        $user = Auth::user();

        $code = (string) random_int(100000, 999999);
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(3);
        $user->save();

        Mail::to($user->email)->send(new TwoFactorCodeMail($code));
    }

    private function redirectBasedOnRole($user)
    {
        $role = $user->role->name;

        switch ($role) {
            case 'cashier':
                return redirect()->route('cashier.dashboard');
            case 'admin':
            case 'manager':
            case 'inventory':
            case 'accountant':
            case 'owner':
            default:
                return redirect()->route('dashboard');
        }
    }
}