<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = $guards ?: [null];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If admin is already logged in, go to admin dashboard
                if ($guard === 'admin' || $request->routeIs('admin.*')) {
                    return redirect()->route('admin.dashboard');
                }
                // Otherwise go to business home/dashboard
                return redirect()->route('home'); // or your business dashboard route
            }
        }

        return $next($request);
    }
}