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

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->loadMissing(['business','customer','items.product','user']);
    }

    public function build()
    {
        $pdf = PDF::loadView('invoices.receipt-pdf', ['invoice' => $this->invoice])->output();

        return $this
            ->subject('Payment Received: Invoice #'.$this->invoice->invoice_number)
            ->view('emails.invoice-receipt')
            ->with(['invoice'=>$this->invoice])
            ->attachData($pdf, 'receipt-'.$this->invoice->invoice_number.'.pdf', ['mime'=>'application/pdf']);
    }
}