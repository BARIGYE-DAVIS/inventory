<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $sale->sale_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        
        /* Business Header */
        .business-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #d1d5db;
        }
        .business-name {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .business-info {
            color: #666;
            font-size: 11px;
            margin: 3px 0;
        }

        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .receipt-number {
            font-size: 16px;
            font-weight: bold;
            color: #4F46E5;
            margin: 5px 0;
        }
        .info-item {
            font-size: 11px;
            margin: 3px 0;
        }
        .customer-name {
            font-size: 13px;
            font-weight: bold;
            margin: 5px 0;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table thead {
            background: #f9fafb;
        }
        table th {
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        table th.text-right {
            text-align: right;
        }
        table td {
            padding: 10px 8px;
            font-size: 11px;
            border-bottom: 1px solid #e5e7eb;
        }
        table td.text-right {
            text-align: right;
        }
        .product-name {
            font-weight: bold;
            margin: 0;
        }
        .product-sku {
            font-size: 10px;
            color: #666;
            margin: 2px 0 0 0;
        }

        /* Totals */
        .totals-section {
            float: right;
            width: 400px;
            margin: 20px 0;
        }
        .total-row {
            padding: 5px 0;
            font-size: 12px;
        }
        .total-row .label {
            display: inline-block;
            width: 60%;
        }
        .total-row .value {
            display: inline-block;
            width: 38%;
            text-align: right;
            font-weight: bold;
        }
        .total-row.discount {
            color: #EF4444;
        }
        .total-row.grand-total {
            font-size: 18px;
            font-weight: bold;
            padding-top: 10px;
            border-top: 2px solid #d1d5db;
            margin-top: 8px;
        }
        .payment-row {
            padding: 5px 0;
            font-size: 11px;
            border-top: 1px solid #e5e7eb;
            margin-top: 8px;
        }

        /* Notes */
        .notes-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9fafb;
            border-radius: 5px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Footer */
        .receipt-footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #d1d5db;
            margin-top: 30px;
            clear: both;
        }
        .thank-you {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .footer-text {
            font-size: 11px;
            color: #666;
            margin: 3px 0;
        }
    </style>
</head>
<body>
    
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

    <!-- Info Grid -->
    <div class="info-grid">
        <!-- Receipt Details -->
        <div class="info-section">
            <div class="section-title">RECEIPT DETAILS</div>
            <div class="receipt-number">{{ $sale->sale_number }}</div>
            <div class="info-item">Date: {{ $sale->sale_date->format('d M Y') }}</div>
            <div class="info-item">Time: {{ $sale->sale_date->format('h:i A') }}</div>
            <div class="info-item">Served by: {{ $sale->user->name }}</div>
        </div>

        <!-- Customer Info -->
        <div class="info-section">
            <div class="section-title">CUSTOMER</div>
            @if($customer)
                <div class="customer-name">{{ $customer->name }}</div>
                @if($customer->phone)
                    <div class="info-item">Phone: {{ $customer->phone }}</div>
                @endif
                @if($customer->email)
                    <div class="info-item">Email: {{ $customer->email }}</div>
                @endif
                @if($customer->address)
                    <div class="info-item">Address: {{ $customer->address }}</div>
                @endif
            @else
                <div class="info-item">Walk-in Customer</div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
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
                    <div class="product-name">{{ $item->product->name }}</div>
                    @if($item->product->sku)
                        <div class="product-sku">{{ $item->product->sku }}</div>
                    @endif
                </td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">UGX {{ number_format($item->unit_price, 0) }}</td>
                <td class="text-right" style="font-weight: bold;">
                    UGX {{ number_format($item->total, 0) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        @php
            $subtotal = $sale->subtotal ?? 0;
            $discountAmount = $sale->discount_amount ?? 0;
            $taxAmount = $sale->tax_amount ?? 0;
            $total = $sale->total ?? 0;
        @endphp
        
        <div class="total-row">
            <span class="label">Subtotal:</span>
            <span class="value">UGX {{ number_format($subtotal, 0) }}</span>
        </div>
        
        <div class="total-row discount">
            <span class="label">Discount:</span>
            <span class="value">
                @if($discountAmount > 0)
                    -UGX {{ number_format($discountAmount, 0) }}
                @else
                    UGX 0
                @endif
            </span>
        </div>

        @if($taxAmount > 0)
        <div class="total-row">
            <span class="label">Tax (18%):</span>
            <span class="value">UGX {{ number_format($taxAmount, 0) }}</span>
        </div>
        @endif

        <div class="total-row grand-total">
            <span class="label">TOTAL:</span>
            <span class="value">UGX {{ number_format($total, 0) }}</span>
        </div>

        <div class="payment-row">
            <span class="label">Payment Method:</span>
            <span class="value">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
        </div>

        <div class="payment-row">
            <span class="label">Payment Status:</span>
            <span class="value">{{ ucfirst($sale->payment_status) }}</span>
        </div>
    </div>

    <!-- Notes -->
    @if($sale->notes)
    <div class="notes-section">
        <div class="notes-title">Notes:</div>
        <div>{{ $sale->notes }}</div>
    </div>
    @endif

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="thank-you">
            Thank you{{ $customer ? ', ' . $customer->name : '' }}!
        </div>
        <div class="footer-text">We appreciate your business. Visit us again!</div>
        @if($business->website)
            <div class="footer-text">{{ $business->website }}</div>
        @endif
    </div>

</body>
</html>