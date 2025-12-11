<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'last_activity_at',
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
        'last_activity_at' => 'datetime',
    ];

    // Automatically hash password when set (only if not already hashed)
    protected function password(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (is_string($value) && $value !== '' && (strlen($value) < 55 || Hash::needsRehash($value))) {
                    return Hash::make($value);
                }
                return $value;
            },
        );
    }

    // Virtual attribute: is_online (last activity within X minutes, default 5)
    public function getIsOnlineAttribute(): bool
    {
        return $this->last_activity_at !== null
            && $this->last_activity_at->gte(now()->subMinutes(5));
    }

    // Scope: only active admins
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Scope: online within N minutes
    public function scopeOnline(Builder $query, int $minutes = 5): Builder
    {
        return $query
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subMinutes($minutes));
    }
}