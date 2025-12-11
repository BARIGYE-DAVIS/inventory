<?php

namespace App\Mail;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope, Address};
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;

    public function __construct(Sale $sale)
    {
        // ✅ ENSURE RELATIONSHIPS ARE LOADED
        $this->sale = $sale->load('items.product', 'customer', 'business');
    }

    public function envelope(): Envelope
    {
        $business = $this->sale->business;
        
        // Use business email if configured, else default
        $fromEmail = $business->hasEmailConfigured() 
            ? $business->smtp_email 
            : config('mail.from.address');

        return new Envelope(
            from: new Address($fromEmail, $business->name),
            replyTo: [
                new Address($business->email ?? $fromEmail, $business->name),
            ],
            subject: 'Receipt #' . $this->sale->sale_number . ' - ' . $business->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sale-receipt',
            with: [
                'sale' => $this->sale,
                'business' => $this->sale->business,
                'customer' => $this->sale->customer,
                'items' => $this->sale->items,
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('receipts.pdf', [
            'sale' => $this->sale,
            'business' => $this->sale->business,
            'customer' => $this->sale->customer,  // ✅ PASS CUSTOMER
            'items' => $this->sale->items,        // ✅ PASS ITEMS
        ]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(), 
                'Receipt-' . $this->sale->sale_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}