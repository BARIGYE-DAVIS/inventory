<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePaidReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $payment;

    public function __construct(Invoice $invoice, $payment)
    {
        $this->invoice = $invoice->loadMissing(['business','customer','items.product','user']);
        $this->payment = $payment;
    }

    public function build()
    {
        $pdf = Pdf::loadView('invoices.receipt-pdf', [
            'invoice' => $this->invoice,
            'payment' => $this->payment,
        ])->output();

        return $this
            ->subject('Payment Received: Invoice #' . $this->invoice->invoice_number)
            ->view('emails.invoice-receipt')
            ->with([
                'invoice' => $this->invoice,
                'payment' => $this->payment,
            ])
            ->attachData($pdf, 'receipt-' . $this->invoice->invoice_number . '.pdf', ['mime' => 'application/pdf']);
    }
}