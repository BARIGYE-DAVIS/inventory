<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // If the request targets admin area, go to admin login
        if ($request->routeIs('admin.*') || $request->is('admin') || $request->is('admin/*')) {
            return route('admin.login');
        }

        // Otherwise, go to business login
        return route('login'); // change if your business login route name differs
    }
}