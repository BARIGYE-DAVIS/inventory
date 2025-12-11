<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * Log user activities for audit trail
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Prepare activity data
            $activityData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'business_id' => $user->business_id,
                'business_name' => $user->business->name ?? 'Unknown',
                'role' => $user->role->name ?? 'Unknown',
                'action' => $request->method(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ];

            // Log different types of activities
            if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
                // Log data modification attempts
                Log::channel('activity')->info('User Data Modification', $activityData);
            } elseif ($request->isMethod('DELETE')) {
                // Log deletion attempts
                Log::channel('activity')->warning('User Data Deletion', $activityData);
            } else {
                // Log read operations (only for sensitive pages)
                $sensitivePaths = ['reports', 'settings', 'staff', 'finance'];
                
                foreach ($sensitivePaths as $path) {
                    if (str_contains($request->path(), $path)) {
                        Log::channel('activity')->info('User Page Access', $activityData);
                        break;
                    }
                }
            }
        }

        return $next($request);
    }
}