@extends('layouts.cashier-layout')

@section('title', 'Customer Details - ' . $customer->name)

@section('page-title')
    <i class="fas fa-user text-green-600 mr-2"></i>Customer Details
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Back Button -->
    <div>
        <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>

    <!-- Customer Info Card -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center space-x-6">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-4xl font-bold text-green-600">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ $customer->name }}</h2>
                    <p class="text-gray-600 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        Customer since {{ $customer->created_at->format('M Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Contact Information</h3>
                <dl class="space-y-3">
                    <div class="flex items-center">
                        <dt class="w-32 text-gray-600">
                            <i class="fas fa-phone text-green-600 mr-2"></i>Phone:
                        </dt>
                        <dd class="font-semibold text-gray-900">{{ $customer->phone }}</dd>
                    </div>
                    @if($customer->email)
                    <div class="flex items-center">
                        <dt class="w-32 text-gray-600">
                            <i class="fas fa-envelope text-green-600 mr-2"></i>Email:
                        </dt>
                        <dd class="font-semibold text-gray-900">{{ $customer->email }}</dd>
                    </div>
                    @endif
                    @if($customer->address)
                    <div class="flex items-start">
                        <dt class="w-32 text-gray-600">
                            <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>Address:
                        </dt>
                        <dd class="font-semibold text-gray-900">{{ $customer->address }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Purchase Statistics</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between p-3 bg-blue-50 rounded-lg">
                        <dt class="text-gray-600">Total Purchases:</dt>
                        <dd class="font-bold text-blue-600">{{ $totalPurchases }}</dd>
                    </div>
                    <div class="flex justify-between p-3 bg-green-50 rounded-lg">
                        <dt class="text-gray-600">Total Spent:</dt>
                        <dd class="font-bold text-green-600">UGX {{ number_format($totalSpent, 0) }}</dd>
                    </div>
                    @if($lastPurchase)
                    <div class="flex justify-between p-3 bg-purple-50 rounded-lg">
                        <dt class="text-gray-600">Last Purchase:</dt>
                        <dd class="font-semibold text-purple-600">{{ $lastPurchase->sale_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Purchase History -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-history text-green-600 mr-2"></i>Purchase History (Last 10)
        </h3>

        @if($customer->sales && $customer->sales->count() > 0)
        <div class="space-y-3">
            @foreach($customer->sales->take(10) as $sale)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $sale->sale_number }}</p>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>{{ $sale->sale_date->format('M d, Y h:i A') }} • 
                        <i class="fas fa-box mr-1"></i>{{ $sale->items->count() }} items
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold text-green-600">UGX {{ number_format($sale->total, 0) }}</p>
                    <div class="space-x-2 mt-2">
                        <a href="{{ route('sales.show', $sale->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="text-sm text-green-600 hover:text-green-800">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No purchases yet</p>
            <a href="{{ route('pos.index') }}" class="inline-block mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus-circle mr-2"></i>Make First Sale
            </a>
        </div>
        @endif
    </div>
</div>
@endsection