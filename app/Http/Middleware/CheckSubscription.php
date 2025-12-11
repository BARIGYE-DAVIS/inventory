<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * Check if business subscription is active
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $business = $user->business;

        // Check if business exists
        if (!$business) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Business not found.');
        }

        // Check if subscription has expired
        if ($business->subscription_expires_at && $business->subscription_expires_at->isPast()) {
            // Log subscription expiry
            Log::info('Subscription expired access attempt', [
                'business_id' => $business->id,
                'business_name' => $business->name,
                'expired_at' => $business->subscription_expires_at,
                'subscription_plan' => $business->subscription_plan,
            ]);

            // Redirect to subscription page or show error
            return redirect()->route('subscription.expired')
                ->with('error', 'Your subscription has expired on ' . $business->subscription_expires_at->format('M d, Y') . '. Please renew to continue using the system.');
        }

        // Check if subscription is expiring soon (within 7 days)
        if ($business->subscription_expires_at && $business->subscription_expires_at->diffInDays(now()) <= 7) {
            session()->flash('warning', 'Your subscription expires in ' . $business->subscription_expires_at->diffInDays(now()) . ' days. Please renew soon.');
        }

        return $next($request);
    }
}