<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $middlewareAliases = [
        // IMPORTANT: use your custom app middlewares
        'auth'  => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        'auth.basic'    => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'  => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'           => \Illuminate\Auth\Middleware\Authorize::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive'  => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'throttle'      => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'      => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Custom aliases
        'tenant'          => \App\Http\Middleware\EnsureUserBelongsToActiveBusiness::class,
        'role'            => \App\Http\Middleware\CheckRole::class,
        'permission'      => \App\Http\Middleware\CheckPermission::class,
        'subscription'    => \App\Http\Middleware\CheckSubscription::class,
        'log.activity'    => \App\Http\Middleware\LogUserActivity::class,
        'business.active' => \App\Http\Middleware\PreventAccessWhenBusinessInactive::class,
        'owner.only'      => \App\Http\Middleware\CheckOwnerOnly::class,

        'user.activity' => \App\Http\Middleware\UpdateUserLastActivity::class,

        // Admin-only auth gate
        'auth.admin' =>\App\Http\Middleware\EnsureAdminAuthenticated::class,
    ];
}