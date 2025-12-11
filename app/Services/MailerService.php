<?php

namespace App\Services;

use Illuminate\Support\Facades\{Config, Mail, Log};
use App\Mail\SaleReceiptMail;
use App\Models\Sale;

class MailerService
{
    /**
     * Send sale receipt using per-business mailer
     */
    public static function sendSaleReceipt(Sale $sale): void
    {
        $business = $sale->business;
        $customer = $sale->customer;

        // If no customer email, skip
        if (!$customer || !$customer->email) {
            Log::warning('Sale has no customer email, skipping receipt', [
                'sale_id' => $sale->id,
            ]);
            return;
        }

        try {
            if ($business && $business->hasEmailConfigured()) {
                // Use business-specific mailer
                $mailerName = 'business_' . $business->id;

                // Copy base SMTP config and override username/password
                $baseMailer = config('mail.mailers.smtp', []);
                
                $dynamicMailer = array_merge($baseMailer, [
                    'username' => $business->smtp_email,
                    'password' => $business->getDecryptedSmtpPassword(),
                ]);

                // Set dynamic mailer at runtime
                Config::set("mail.mailers.{$mailerName}", $dynamicMailer);

                // Send using dynamic mailer
                Mail::mailer($mailerName)
                    ->to($customer->email)
                    ->send(new SaleReceiptMail($sale));

                Log::info('Receipt sent via business email', [
                    'sale_id' => $sale->id,
                    'from' => $business->smtp_email,
                    'to' => $customer->email,
                ]);
            } else {
                // Fallback to default system mailer
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
            
            // Don't throw - email failure shouldn't break the sale
        }
    }
}