<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOwnerOnly
{
    /**
     * Handle an incoming request.
     *
     * Only business owners can access
     *
     * Usage: Route::middleware(['auth', 'tenant', 'owner.only'])
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is the business owner
        if (!$user->isOwner()) {
            Log::warning('Non-owner access attempt to owner-only page', [
                'user_id' => $user->id,
                'user_role' => $user->role->name,
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'Unauthorized. This page is only accessible to business owners.');
        }

        return $next($request);
    }
}