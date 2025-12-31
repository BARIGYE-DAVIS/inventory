<?php

namespace App\Services;

use Illuminate\Support\Facades\{Config, Mail, Log};
use App\Mail\SaleReceiptMail;
use App\Mail\InvoiceCreatedMail;
use App\Mail\InvoicePaidReceiptMail;
use App\Models\Sale;
use App\Models\Invoice;

class MailerService
{
    /**
     * Send a sale receipt PDF/email using per-business mailer settings if present.
     */
    public static function sendSaleReceipt(Sale $sale): void
    {
        $business = $sale->business;
        $customer = $sale->customer;

        if (!$customer || !$customer->email) {
            Log::warning('Sale has no customer email, skipping receipt', [
                'sale_id' => $sale->id,
            ]);
            return;
        }

        try {
            if ($business && $business->hasEmailConfigured()) {
                $mailerName = 'business_' . $business->id;
                $baseMailer = config('mail.mailers.smtp', []);
                $dynamicMailer = array_merge($baseMailer, [
                    'username' => $business->smtp_email,
                    'password' => $business->getDecryptedSmtpPassword(),
                ]);
                Config::set("mail.mailers.{$mailerName}", $dynamicMailer);

                Mail::mailer($mailerName)
                    ->to($customer->email)
                    ->send(new SaleReceiptMail($sale));

                Log::info('Receipt sent via business email', [
                    'sale_id' => $sale->id,
                    'from' => $business->smtp_email,
                    'to' => $customer->email,
                ]);
            } else {
                Mail::to($customer->email)
                    ->send(new SaleReceiptMail($sale));
                Log::info('Receipt sent via default email', [
                    'sale_id' => $sale->id,
                    'to' => $customer->email,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send receipt email', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
            ]);
            // Email failure shouldn't break the sale
        }
    }

    /**
     * Send an invoice PDF/email using per-business mailer settings if present.
     */
    public static function sendInvoice(Invoice $invoice): void
    {
        $business = $invoice->business;
        $customer = $invoice->customer;

        if (!$customer || !$customer->email) {
            Log::warning('Invoice has no customer email, skipping invoice send', [
                'invoice_id' => $invoice->id,
            ]);
            return;
        }

        try {
            if ($business && $business->hasEmailConfigured()) {
                $mailerName = 'business_' . $business->id;
                $baseMailer = config('mail.mailers.smtp', []);
                $dynamicMailer = array_merge($baseMailer, [
                    'username' => $business->smtp_email,
                    'password' => $business->getDecryptedSmtpPassword(),
                ]);
                Config::set("mail.mailers.{$mailerName}", $dynamicMailer);

                Mail::mailer($mailerName)
                    ->to($customer->email)
                    ->send(new InvoiceCreatedMail($invoice));

                Log::info('Invoice sent via business email', [
                    'invoice_id' => $invoice->id,
                    'from' => $business->smtp_email,
                    'to' => $customer->email,
                ]);
            } else {
                Mail::to($customer->email)
                    ->send(new InvoiceCreatedMail($invoice));
                Log::info('Invoice sent via default email', [
                    'invoice_id' => $invoice->id,
                    'to' => $customer->email,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            // Email failure shouldn't break invoice creation
        }
    }

    /**
     * Send a PAID invoice receipt email/PDF using per-business mailer settings.
     */
    public static function sendInvoiceReceipt(Invoice $invoice): void
    {
        $business = $invoice->business;
        $customer = $invoice->customer;

        if (!$customer || !$customer->email) {
            Log::warning('Invoice has no customer email, skipping paid receipt', [
                'invoice_id' => $invoice->id,
            ]);
            return;
        }

        try {
            if ($business && $business->hasEmailConfigured()) {
                $mailerName = 'business_' . $business->id;
                $baseMailer = config('mail.mailers.smtp', []);
                $dynamicMailer = array_merge($baseMailer, [
                    'username' => $business->smtp_email,
                    'password' => $business->getDecryptedSmtpPassword(),
                ]);
                Config::set("mail.mailers.{$mailerName}", $dynamicMailer);

                Mail::mailer($mailerName)
                    ->to($customer->email)
                    ->send(new InvoicePaidReceiptMail($invoice));

                Log::info('Paid receipt sent via business email', [
                    'invoice_id' => $invoice->id,
                    'from' => $business->smtp_email,
                    'to' => $customer->email,
                ]);
            } else {
                Mail::to($customer->email)
                    ->send(new InvoicePaidReceiptMail($invoice));
                Log::info('Paid receipt sent via default email', [
                    'invoice_id' => $invoice->id,
                    'to' => $customer->email,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send paid invoice receipt email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            // Email failure shouldn't break paid flow
        }
    }
}