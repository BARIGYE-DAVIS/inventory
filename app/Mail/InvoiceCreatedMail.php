<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->loadMissing(['business', 'customer', 'items.product', 'user']);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Generate PDF from Blade
        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice])->output();

        return $this->subject('Your Invoice #' . $this->invoice->invoice_number)
                    ->view('emails.invoice-created')
                    ->with([
                        'invoice' => $this->invoice,
                    ])
                    ->attachData($pdf, 'invoice-' . $this->invoice->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}