<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Check if user has specific permission
     *
     * Usage: Route::middleware(['auth', 'tenant', 'permission:create_products'])
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to continue.');
        }
        $user = Auth::user();
       

        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized permission access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role->name,
                'required_permission' => $permission,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            abort(403, 'Unauthorized. You do not have permission to perform this action. Required: ' . $permission);
        }

        return $next($request);
    }
}