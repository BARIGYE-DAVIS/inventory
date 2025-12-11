<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Admin guard
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        // API guards (optional)
        // 'api' => [
        //     'driver' => 'token',
        //     'provider' => 'users',
        // ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // Admin provider (points to your Admin model)
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Admin password reset config (optional, if you use password resets for admins)
        'admins' => [
            'provider' => 'admins',
            'table' => 'admin_password_reset_tokens', // you can reuse the users table if you prefer
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];