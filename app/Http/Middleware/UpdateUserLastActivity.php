<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateUserLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {
            $user = auth()->user();
            $last = session('user_last_activity_write_at');
            if (!$last || now()->diffInSeconds($last) > 60) {
                $user->forceFill(['last_activity_at' => now()])->saveQuietly();
                session(['user_last_activity_write_at' => now()]);
            }
        }

        return $response;
    }
}