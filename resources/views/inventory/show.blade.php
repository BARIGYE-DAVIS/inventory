@extends('layouts.app')

@section('title', 'Product Stock Details - ' . $product->name)

@section('page-title')
    <i class="fas fa-box text-indigo-600 mr-2"></i>{{ $product->name }} - Stock Details
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Back Button -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('inventory.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center space-x-1">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Inventory</span>
        </a>
    </div>

    <!-- Product Info Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left: Basic Info -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $activity['category'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">SKU/Code</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $product->sku ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Reorder Level</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($product->reorder_level, 0) }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: Pricing -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Pricing Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Cost Price</p>
                        <p class="text-lg font-semibold text-gray-900">UGX {{ number_format($activity['cost_price'], 0) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Selling Price</p>
                        <p class="text-lg font-semibold text-gray-900">UGX {{ number_format($activity['selling_price'], 0) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Profit Margin per Unit</p>
                        <p class="text-lg font-semibold text-green-600">UGX {{ number_format($activity['selling_price'] - $activity['cost_price'], 0) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <!-- Opening Stock -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Opening Stock</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($activity['opening_stock'], 0) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-inbox text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Purchases -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Purchases</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">{{ number_format($activity['purchases'], 0) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-truck text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Sales -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Sales</p>
                    <p class="text-2xl font-bold text-orange-600 mt-2">{{ number_format($activity['sales'], 0) }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-shopping-cart text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Current Stock -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Current Stock</p>
                    <p class="text-2xl font-bold text-purple-600 mt-2">{{ number_format($activity['current_stock'], 0) }}</p>
                    @if($activity['current_stock'] <= 0)
                        <span class="text-xs text-red-600 font-semibold mt-1">Out of Stock</span>
                    @elseif($activity['current_stock'] <= $product->reorder_level)
                        <span class="text-xs text-yellow-600 font-semibold mt-1">Low Stock</span>
                    @endif
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-boxes text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Value Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Opening Value -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Opening Value</p>
                    <p class="text-xl font-bold text-blue-600 mt-2">UGX {{ number_format($activity['opening_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-blue-300 text-3xl"></i>
            </div>
        </div>

        <!-- Purchases Value -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Purchases Value</p>
                    <p class="text-xl font-bold text-green-600 mt-2">UGX {{ number_format($activity['purchases_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-green-300 text-3xl"></i>
            </div>
        </div>

        <!-- Sales Value (Revenue) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Sales Value (Revenue)</p>
                    <p class="text-xl font-bold text-orange-600 mt-2">UGX {{ number_format($activity['sales_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-orange-300 text-3xl"></i>
            </div>
        </div>

        <!-- Current Value -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Current Stock Value</p>
                    <p class="text-xl font-bold text-purple-600 mt-2">UGX {{ number_format($activity['current_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-purple-300 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Stock Movement Analysis -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Stock Movement Analysis
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Turnover Rate -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4">
                <p class="text-sm text-indigo-600 font-semibold mb-2">Turnover Rate</p>
                <p class="text-3xl font-bold text-indigo-900">
                    @if($activity['opening_stock'] > 0)
                        {{ number_format(($activity['sales'] / $activity['opening_stock']) * 100, 2) }}%
                    @else
                        N/A
                    @endif
                </p>
                <p class="text-xs text-indigo-700 mt-2">Percentage of opening stock sold</p>
            </div>

            <!-- Stock Health -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                <p class="text-sm text-blue-600 font-semibold mb-2">Stock Health</p>
                <p class="text-3xl font-bold text-blue-900">
                    @if($activity['current_stock'] > $product->reorder_level)
                        ✓ Healthy
                    @elseif($activity['current_stock'] > 0)
                        ⚠ Low
                    @else
                        ✗ Out
                    @endif
                </p>
                <p class="text-xs text-blue-700 mt-2">Based on reorder level</p>
            </div>

            <!-- Net Stock Change -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                <p class="text-sm text-purple-600 font-semibold mb-2">Net Stock Change</p>
                <p class="text-3xl font-bold text-purple-900">
                    @php
                        $netChange = $activity['purchases'] - $activity['sales'];
                    @endphp
                    {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange, 0) }}
                </p>
                <p class="text-xs text-purple-700 mt-2">Purchases minus sales</p>
            </div>
        </div>

        <!-- Detailed Calculation -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-sm font-semibold text-gray-700 mb-3">Calculation Breakdown:</p>
            <div class="space-y-2 text-sm text-gray-700">
                <p>
                    <span class="font-semibold">Opening Stock:</span> 
                    <span class="text-blue-600">{{ number_format($activity['opening_stock'], 0) }} units</span>
                </p>
                <p class="flex items-center">
                    <span class="font-semibold mr-2">+</span>
                    <span class="font-semibold">Purchases:</span> 
                    <span class="text-green-600 ml-auto">{{ number_format($activity['purchases'], 0) }} units</span>
                </p>
                <p class="flex items-center">
                    <span class="font-semibold mr-2">−</span>
                    <span class="font-semibold">Sales:</span> 
                    <span class="text-orange-600 ml-auto">{{ number_format($activity['sales'], 0) }} units</span>
                </p>
                <div class="border-t border-gray-300 pt-2 mt-2">
                    <p class="flex items-center font-bold text-gray-900">
                        <span class="font-semibold mr-2">=</span>
                        <span>Current Stock:</span> 
                        <span class="text-purple-600 ml-auto">{{ number_format($activity['current_stock'], 0) }} units</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Links -->
    <div class="flex flex-col md:flex-row gap-4">
        <a href="{{ route('inventory.index') }}" class="flex items-center justify-center space-x-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-boxes"></i>
            <span>Back to Inventory</span>
        </a>
        <a href="{{ route('inventory.activities') }}" class="flex items-center justify-center space-x-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-history"></i>
            <span>View All Activities</span>
        </a>
    </div>

</div>
@endsection
