<!-- resources/views/invoices/receipt-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.09);
        }
        .receipt-content {
            padding: 40px;
        }
        .business-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #d1d5db;
        }
        .business-name {
            font-size: 28px;
            font-weight: bold;
            color: #111827;
            margin: 10px 0;
        }
        .business-info {
            color: #6B7280;
            font-size: 14px;
            margin: 5px 0;
        }
        .receipt-badge {
            display: inline-block;
            margin-top: 18px;
            margin-bottom: 8px;
            padding: 6px 18px;
            background: #16a34a;
            color: #fff;
            border-radius: 99px;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            box-shadow: 0 2px 4px rgba(22,163,74,0.10);
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-section { margin: 0; }
        .section-title {
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .receipt-number {
            font-size: 18px;
            font-weight: bold;
            color: #4F46E5;
            margin: 8px 0 5px 0;
        }
        .info-item {
            font-size: 14px;
            color: #4B5563;
            margin: 5px 0;
        }
        .customer-name {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 5px 0;
        }
        .items-section {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table thead {
            background: #F9FAFB;
        }
        table th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        table th.text-right { text-align: right; }
        table td {
            padding: 15px 12px;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }
        table td.text-right { text-align: right; }
        .product-name {
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .product-sku {
            font-size: 12px;
            color: #6B7280;
            margin: 2px 0 0 0;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .totals-box {
            width: 400px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        .total-row.discount { color: #EF4444; }
        .total-row.balance { color: #b7791f; font-weight: bold; }
        .total-row.grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            padding-top: 15px;
            border-top: 2px solid #d1d5db;
            margin-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 9999px;
        }
        .status-paid { background: #D1FAE5; color: #065F46; }
        .status-unpaid { background: #FEF3C7; color: #92400E; }
        .notes-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #F9FAFB;
            border-radius: 8px;
        }
        .notes-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        .notes-text {
            font-size: 14px;
            color: #6B7280;
            margin: 0;
        }
        .receipt-footer {
            text-align: center;
            padding-top: 30px;
            border-top: 2px solid #d1d5db;
        }
        .thank-you {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
        }
        .footer-text {
            font-size: 14px;
            color: #6B7280;
            margin: 5px 0;
        }
        @media only screen and (max-width: 600px) {
            .receipt-content { padding: 20px; }
            .info-grid { grid-template-columns: 1fr; gap: 20px; }
            .totals-box { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="receipt-content">
            <!-- Business Header -->
            <div class="business-header">
                <div class="business-name">{{ $invoice->business->name }}</div>
                @if($invoice->business->address)
                    <div class="business-info">{{ $invoice->business->address }}</div>
                @endif
                <div class="business-info">
                    @if($invoice->business->phone)
                        Tel: {{ $invoice->business->phone }}
                    @endif
                    @if($invoice->business->email)
                        | Email: {{ $invoice->business->email }}
                    @endif
                </div>
                <div class="receipt-badge">PAYMENT RECEIPT</div>

                {{-- Payment Status Summary --}}
                @if($invoice->balance == 0)
                    <div style="background:#e6ffed; color:#22543d; font-weight:bold; font-size:18px; margin-bottom:18px; padding:14px 0; border-left:4px solid #22c55e;">
                        Your outstanding amount of UGX {{ number_format($invoice->total) }} has been <u>fully paid</u>.<br>
                        <strong>Your balance is UGX 0</strong>
                    </div>
                @else
                    <div style="background:#fffbe6; color:#b7791f; font-weight:bold; font-size:18px; margin-bottom:18px; padding:14px 0; border-left:4px solid #facc15;">
                        Your outstanding amount of UGX {{ number_format($invoice->total) }} has been <u>partially paid</u>.<br>
                        Amount paid: <strong>UGX {{ number_format($payment->amount_paid) }}</strong><br>
                        Your balance is <strong>UGX {{ number_format($invoice->balance) }}</strong>
                    </div>
                @endif
            </div>

            <!-- Receipt Info Grid -->
            <div class="info-grid">
                <div class="info-section">
                    <div class="section-title">RECEIPT DETAILS</div>
                    <div class="receipt-number">Invoice #{{ $invoice->invoice_number }}</div>
                    <div class="info-item">
                        Payment Date:
                        {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : $invoice->updated_at->format('d M Y') }}
                    </div>
                    <div class="info-item">
                        Generated by: {{ $invoice->user->name ?? '---' }}
                    </div>
                </div>
                <div class="info-section">
                    <div class="section-title">CUSTOMER</div>
                    @if($invoice->customer)
                        <div class="customer-name">{{ $invoice->customer->name }}</div>
                        @if($invoice->customer->phone)
                            <div class="info-item">📞 {{ $invoice->customer->phone }}</div>
                        @endif
                        @if($invoice->customer->email)
                            <div class="info-item">📧 {{ $invoice->customer->email }}</div>
                        @endif
                        @if($invoice->customer->address)
                            <div class="info-item">📍 {{ $invoice->customer->address }}</div>
                        @endif
                    @else
                        <div class="info-item">Walk-in Customer</div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="items-section">
                <div class="section-title">ITEMS</div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <p class="product-name">{{ $item->description ?? ($item->product->name ?? '') }}</p>
                                @if($item->product && $item->product->sku)
                                    <p class="product-sku">{{ $item->product->sku }}</p>
                                @endif
                            </td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->discount ?? 0, 2) }}</td>
                            <td class="text-right" style="font-weight:600;">
                                {{ number_format(($item->unit_price - ($item->discount ?? 0)) * $item->quantity, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="totals-section">
                <div class="totals-box">
                    <div class="total-row">
                        <span style="color: #6B7280;">Subtotal:</span>
                        <span style="font-weight: 600;">
                            {{ number_format($invoice->total ?? 0, 2) }}
                        </span>
                    </div>
                    <div class="total-row discount">
                        <span>Discount:</span>
                        <span style="font-weight: 600;">
                            @if(($invoice->discount_amount ?? 0) > 0)
                                -{{ number_format($invoice->discount_amount, 2) }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                    @if(($invoice->tax_amount ?? 0) > 0)
                        <div class="total-row">
                            <span style="color: #6B7280;">Tax (18%):</span>
                            <span style="font-weight: 600;">
                                {{ number_format($invoice->tax_amount, 2) }}
                            </span>
                        </div>
                    @endif
                    <div class="total-row">
                        <span><b>Total Paid NOW:</b></span>
                        <span style="font-weight: 600; color: #16a34a">
                            {{ number_format($payment->amount_paid, 2) }}
                        </span>
                    </div>
                    <div class="total-row">
                        <span><b>Paid (all time):</b></span>
                        <span style="font-weight: 600; color: #2563eb">
                            {{ number_format($invoice->paid, 2) }}
                        </span>
                    </div>
                    <div class="total-row balance">
                        <span><b>Balance Remaining:</b></span>
                        <span>{{ number_format($invoice->balance, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
            <div class="notes-section">
                <div class="notes-title">Notes:</div>
                <p class="notes-text">{{ $invoice->notes }}</p>
            </div>
            @endif

            <!-- Footer -->
            <div class="receipt-footer">
                <div class="thank-you">
                    Thank you{{ $invoice->customer ? ', ' . $invoice->customer->name : '' }}!
                </div>
                <div class="footer-text">We appreciate your business. Visit us again!</div>
                @if($invoice->business->website)
                    <div class="footer-text" style="margin-top: 10px;">{{ $invoice->business->website }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>