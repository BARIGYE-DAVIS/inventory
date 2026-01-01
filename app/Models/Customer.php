<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'address',
        'credit_limit',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            Invoice::class,
            'customer_id',   // Foreign key on Invoice table...
            'invoice_id',    // Foreign key on Payment table...
            'id',            // Local key on Customer table...
            'id'             // Local key on Invoice table...
        );
    }

    public function getTotalPurchases(): float
    {
        return $this->sales()->sum('total');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}