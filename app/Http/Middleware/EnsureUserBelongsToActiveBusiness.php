<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToActiveBusiness
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        // Check if user has a business
        if (!$user->business) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account is not associated with any business.');
        }

        // Check if business is active
        if (!$user->business->isActive()) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your business account is inactive or expired. Please contact support or renew your subscription.');
        }

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact your business owner.');
        }

        // Store business_id in session for easy access throughout the app
        session([
            'business_id' => $user->business_id,
            'business_name' => $user->business->name,
            'user_role' => $user->role->name,
        ]);

        return $next($request);
    }
}