<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->sale_number }}</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .receipt-content {
            padding: 40px;
        }
        
        /* Business Header */
        .business-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #d1d5db;
        }
        .business-logo {
            width: 80px;
            height: 80px;
            background: #4F46E5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 36px;
            font-weight: bold;
            color: white;
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

        /* Receipt Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-section {
            margin: 0;
        }
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
            margin: 5px 0;
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

        /* Items Table */
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
        table th.text-right {
            text-align: right;
        }
        table td {
            padding: 15px 12px;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }
        table td.text-right {
            text-align: right;
        }
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

        /* Totals Section */
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
        .total-row.discount {
            color: #EF4444;
        }
        .total-row.grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            padding-top: 15px;
            border-top: 2px solid #d1d5db;
            margin-top: 10px;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            margin-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 9999px;
        }
        .status-paid {
            background: #D1FAE5;
            color: #065F46;
        }
        .status-unpaid {
            background: #FEF3C7;
            color: #92400E;
        }

        /* Notes */
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

        /* Footer */
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

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .receipt-content {
                padding: 20px;
            }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .totals-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="receipt-content">
            
            <!-- Business Header -->
            <div class="business-header">
 
                <div class="business-name">{{ $business->name }}</div>
                @if($business->address)
                    <div class="business-info">{{ $business->address }}</div>
                @endif
                <div class="business-info">
                    @if($business->phone)
                        Tel: {{ $business->phone }}
                    @endif
                    @if($business->email)
                        | Email: {{ $business->email }}
                    @endif
                </div>
            </div>

            <!-- Receipt Info Grid -->
            <div class="info-grid">
                <!-- Receipt Details -->
                <div class="info-section">
                    <div class="section-title">RECEIPT DETAILS</div>
                    <div class="receipt-number">{{ $sale->sale_number }}</div>
                    <div class="info-item">
                        📅 {{ $sale->sale_date->format('d M Y') }}
                    </div>
                    <div class="info-item">
                        🕐 {{ $sale->sale_date->format('h:i A') }}
                    </div>
                    <div class="info-item">
                        👤 Served by: {{ $sale->user->name }}
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="info-section">
                    <div class="section-title">CUSTOMER</div>
                    @if($customer)
                        <div class="customer-name">{{ $customer->name }}</div>
                        @if($customer->phone)
                            <div class="info-item">📞 {{ $customer->phone }}</div>
                        @endif
                        @if($customer->email)
                            <div class="info-item">📧 {{ $customer->email }}</div>
                        @endif
                        @if($customer->address)
                            <div class="info-item">📍 {{ $customer->address }}</div>
                        @endif
                    @else
                        <div class="info-item">Walk-in Customer</div>
                    @endif
                </div>
            </div>

            <!-- Items Purchased -->
            <div class="items-section">
                <div class="section-title">ITEMS PURCHASED</div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <p class="product-name">{{ $item->product->name }}</p>
                                @if($item->product->sku)
                                    <p class="product-sku">{{ $item->product->sku }}</p>
                                @endif
                            </td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">UGX {{ number_format($item->unit_price, 0) }}</td>
                            <td class="text-right" style="font-weight: 600;">
                                UGX {{ number_format($item->total, 0) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="totals-section">
                <div class="totals-box">
                    @php
                        $subtotal = $sale->subtotal ?? 0;
                        $discountAmount = $sale->discount_amount ?? 0;
                        $taxAmount = $sale->tax_amount ?? 0;
                        $total = $sale->total ?? 0;
                    @endphp
                    
                    <!-- Subtotal -->
                    <div class="total-row">
                        <span style="color: #6B7280;">Subtotal:</span>
                        <span style="font-weight: 600;">UGX {{ number_format($subtotal, 0) }}</span>
                    </div>
                    
                    <!-- Discount -->
                    <div class="total-row discount">
                        <span>Discount:</span>
                        <span style="font-weight: 600;">
                            @if($discountAmount > 0)
                                -UGX {{ number_format($discountAmount, 0) }}
                            @else
                                UGX 0
                            @endif
                        </span>
                    </div>

                    <!-- Tax -->
                    @if($taxAmount > 0)
                    <div class="total-row">
                        <span style="color: #6B7280;">Tax (18%):</span>
                        <span style="font-weight: 600;">UGX {{ number_format($taxAmount, 0) }}</span>
                    </div>
                    @endif

                    <!-- Grand Total -->
                    <div class="total-row grand-total">
                        <span>TOTAL:</span>
                        <span>UGX {{ number_format($total, 0) }}</span>
                    </div>

                    <!-- Payment Method -->
                    <div class="payment-row">
                        <span style="color: #6B7280;">Payment Method:</span>
                        <span style="font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
                    </div>

                    <!-- Payment Status -->
                    <div class="payment-row">
                        <span style="color: #6B7280;">Payment Status:</span>
                        <span class="status-badge {{ $sale->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($sale->notes)
            <div class="notes-section">
                <div class="notes-title">Notes:</div>
                <p class="notes-text">{{ $sale->notes }}</p>
            </div>
            @endif

            <!-- Footer -->
            <div class="receipt-footer">
                <div class="thank-you">
                    Thank you{{ $customer ? ', ' . $customer->name : '' }}!
                </div>
                <div class="footer-text">We appreciate your business. Visit us again!</div>
                @if($business->website)
                    <div class="footer-text" style="margin-top: 10px;">{{ $business->website }}</div>
                @endif
            </div>

        </div>
    </div>
</body>
</html>