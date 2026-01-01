<?php

namespace App\Mail;

use App\Models\Business;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PeriodClosedMail extends Mailable
{
    public function __construct(
        public User $user,
        public Business $business,
        public Carbon $periodStart,
        public Carbon $periodEnd,
        public int $totalProducts,
        public float $totalVariance,
        public float $overstock,
        public float $shortage,
        public int $productsWithVariance
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->user->email],
            subject: "Inventory Period Closed - {$this->business->name} ({$this->periodStart->format('M Y')})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.period-closed',
            with: [
                'user' => $this->user,
                'business' => $this->business,
                'periodStart' => $this->periodStart,
                'periodEnd' => $this->periodEnd,
                'totalProducts' => $this->totalProducts,
                'totalVariance' => $this->totalVariance,
                'overstock' => $this->overstock,
                'shortage' => $this->shortage,
                'productsWithVariance' => $this->productsWithVariance,
                'variancePercentage' => $this->totalProducts > 0 ? ($this->totalVariance / $this->totalProducts) * 100 : 0,
            ]
        );
    }
}
