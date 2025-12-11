<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'is_superadmin',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_superadmin' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'two_factor_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];
}