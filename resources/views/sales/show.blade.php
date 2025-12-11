@extends('layouts.app')

@section('title', 'Receipt - ' . $sale->sale_number)

@section('page-title')
    <i class="fas fa-receipt text-indigo-600 mr-2"></i>Receipt - {{ $sale->sale_number }}
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Print Button (Hidden when printing) -->
    <div class="no-print mb-4 flex justify-between items-center">
        <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Back to Sales
        </a>
        <button onclick="window.print()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <i class="fas fa-print mr-2"></i>Print Receipt
        </button>
    </div>

    <!-- ✅ PROFESSIONAL RECEIPT -->
    <div class="bg-white rounded-xl shadow-lg p-8" id="receipt">
        
        <!-- Business Header -->
        <div class="text-center mb-6 pb-6 border-b-2 border-gray-300">
            @if(auth()->user()->business->logo)
                <img src="{{ asset('storage/' . auth()->user()->business->logo) }}" 
                     alt="{{ auth()->user()->business->name }}" 
                     class="h-20 mx-auto mb-3">
            @else
                <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-3xl font-bold text-white">
                        {{ substr(auth()->user()->business->name, 0, 1) }}
                    </span>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->business->name }}</h1>
            @if(auth()->user()->business->address)
                <p class="text-gray-600">{{ auth()->user()->business->address }}</p>
            @endif
            <p class="text-gray-600">
                @if(auth()->user()->business->phone)
                    Tel: {{ auth()->user()->business->phone }}
                @endif
                @if(auth()->user()->business->email)
                    | Email: {{ auth()->user()->business->email }}
                @endif
            </p>
        </div>

        <!-- Receipt Info -->
        <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
            <div>
                <p class="text-sm text-gray-500 font-semibold mb-2">RECEIPT DETAILS</p>
                <p class="font-bold text-lg text-indigo-600">{{ $sale->sale_number }}</p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $sale->sale_date->format('d M Y') }}
                </p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $sale->sale_date->format('h:i A') }}
                </p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-user mr-1"></i>
                    Served by: {{ $sale->user->name }}
                </p>
            </div>

            <!-- ✅ CUSTOMER INFO (If Provided) -->
            @if($sale->customer)
            <div>
                <p class="text-sm text-gray-500 font-semibold mb-2">CUSTOMER</p>
                <p class="font-semibold text-gray-900">{{ $sale->customer->name }}</p>
                @if($sale->customer->phone)
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-phone mr-1"></i>
                        {{ $sale->customer->phone }}
                    </p>
                @endif
                @if($sale->customer->email)
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-envelope mr-1"></i>
                        {{ $sale->customer->email }}
                    </p>
                @endif
                @if($sale->customer->address)
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ $sale->customer->address }}
                    </p>
                @endif
            </div>
            @else
            <div>
                <p class="text-sm text-gray-500 font-semibold mb-2">CUSTOMER</p>
                <p class="text-gray-600">Walk-in Customer</p>
            </div>
            @endif
        </div>

        <!-- ✅ ITEMS PURCHASED -->
        <div class="mb-6">
            <p class="text-sm text-gray-500 font-semibold mb-3">ITEMS PURCHASED</p>
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sale->items as $index => $item)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->product->sku }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">
                            {{ $item->quantity }} {{ $item->product->unit }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">
                            UGX {{ number_format($item->unit_price, 0) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                            UGX {{ number_format($item->total, 0) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ✅ TOTALS (ALWAYS SHOW DISCOUNT) -->
        <!-- ✅ TOTALS SECTION (Using 'discount_amount' column) -->
<div class="flex justify-end mb-6">
    <div class="w-80 space-y-2">
        @php
            // Use correct column names from database
            $subtotal = $sale->subtotal ?? 0;
            $discountAmount = $sale->discount_amount ?? 0;  // ✅ Using 'discount_amount'
            $taxAmount = $sale->tax_amount ?? 0;            // ✅ Using 'tax_amount'
            $total = $sale->total ?? 0;
        @endphp
        
        <!-- Subtotal -->
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Subtotal:</span>
            <span class="font-semibold">UGX {{ number_format($subtotal, 0) }}</span>
        </div>
        
        <!-- Discount (Always show, even if 0) -->
        <div class="flex justify-between text-sm {{ $discountAmount > 0 ? 'text-red-600' : 'text-gray-600' }}">
            <span>Discount:</span>
            <span class="font-semibold">
                @if($discountAmount > 0)
                    -UGX {{ number_format($discountAmount, 0) }}
                @else
                    UGX 0
                @endif
            </span>
        </div>

        <!-- Tax (if applicable) -->
        @if($taxAmount > 0)
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Tax (18%):</span>
            <span class="font-semibold">UGX {{ number_format($taxAmount, 0) }}</span>
        </div>
        @endif

        <!-- Total -->
        <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t-2 border-gray-300">
            <span>TOTAL:</span>
            <span>UGX {{ number_format($total, 0) }}</span>
        </div>

        <!-- Payment Method -->
        <div class="flex justify-between text-sm pt-2 border-t">
            <span class="text-gray-600">Payment Method:</span>
            <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
        </div>

        <!-- Payment Status -->
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Payment Status:</span>
            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                {{ $sale->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ ucfirst($sale->payment_status) }}
            </span>
        </div>
    </div>
</div>
        <!-- Notes -->
        @if($sale->notes)
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-semibold text-gray-700 mb-1">Notes:</p>
            <p class="text-sm text-gray-600">{{ $sale->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center pt-6 border-t-2 border-gray-300">
            <p class="text-lg font-semibold text-gray-900 mb-2">
                Thank you{{ $sale->customer ? ', ' . $sale->customer->name : '' }}!
            </p>
            <p class="text-sm text-gray-600">We appreciate your business. Visit us again!</p>
            @if(auth()->user()->business->website)
                <p class="text-sm text-gray-600 mt-2">{{ auth()->user()->business->website }}</p>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #receipt, #receipt * {
            visibility: visible;
        }
        #receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .bg-white {
            box-shadow: none !important;
        }
    }
</style>
@endpush
@endsection