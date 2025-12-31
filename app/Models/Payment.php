<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',        // the cashier/user who received/processed the payment
        'amount_paid',
        'paid_at',        // date/time of payment
        'notes',
    ];

    // The invoice this payment relates to
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // The user who received/processed the payment
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}