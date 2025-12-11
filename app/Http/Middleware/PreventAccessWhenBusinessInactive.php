<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PreventAccessWhenBusinessInactive
{
    /**
     * Handle an incoming request.
     *
     * Prevent access if business is deactivated by admin
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $business = Auth::user()->business;

        // Check if business is_active flag is false
        if (!$business->is_active) {
            Auth::logout();

            return redirect()->route('login')
                ->with('error', 'Your business account has been deactivated by the administrator. Please contact support for assistance.');
        }

        return $next($request);
    }
}