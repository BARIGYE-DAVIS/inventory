@extends('layouts.app')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Inventory Overview</h1>
            <p class="text-gray-600">Track opening stock, sales, and current stock levels with transaction history</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>Select Location
                    </label>
                    <select id="locationFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                        <option value="">-- All Locations --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $selectedLocation == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                                @if($location->is_main) (Main) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-box text-indigo-600 mr-2"></i>Product
                    </label>
                    <select id="productFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                        <option value="">-- All Products --</option>
                        @foreach($inventoryOverview as $item)
                            <option value="{{ $item['id'] }}" {{ $selectedProduct == $item['id'] ? 'selected' : '' }}>
                                {{ $item['name'] }} ({{ $item['sku'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Location Summary Card -->
        @if($currentLocation)
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">{{ $currentLocation->name }}</h2>
                    @if($currentLocation->is_main)
                        <span class="inline-block mt-2 px-3 py-1 bg-indigo-400 rounded-full text-sm font-semibold">Main Location</span>
                    @endif
                    @if($currentLocation->address)
                        <p class="text-indigo-100 mt-2">{{ $currentLocation->address }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-indigo-100 text-sm">Total Products</p>
                    <p class="text-4xl font-bold">{{ count($inventoryOverview) }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Inventory Overview Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Opening Stock</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Sales</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Current Stock</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Unit Price</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Stock Value</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($inventoryOverview as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $item['name'] }}</p>
                                        <p class="text-sm text-gray-500">SKU: {{ $item['sku'] }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item['category'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                        {{ $item['opening_stock'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-semibold">
                                        {{ $item['total_sold'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item['current_stock'] <= 0)
                                        <span class="inline-flex items-center justify-center px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                            {{ $item['current_stock'] }}
                                        </span>
                                    @elseif($item['current_stock'] <= 10)
                                        <span class="inline-flex items-center justify-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                            {{ $item['current_stock'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                            {{ $item['current_stock'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-gray-900 font-semibold">
                                    ₱{{ number_format($item['unit_price'], 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-semibold text-gray-900">₱{{ number_format($item['value'], 2) }}</span>
                                    <p class="text-xs text-gray-500">Cost: ₱{{ number_format($item['cost_value'], 2) }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('inventory.overview', ['location' => $selectedLocation, 'product' => $item['id']]) }}" class="inline-block px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors text-sm font-medium">
                                        <i class="fas fa-eye mr-1"></i>Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3 block"></i>
                                        <p class="text-lg font-medium">No inventory data available</p>
                                        <p class="text-sm">Try selecting a different location or add products to inventory</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales Transaction History (for selected product) -->
        @if($selectedProduct && count($recentSalesTransactions) > 0)
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-history text-indigo-600 mr-2"></i>Recent Sales Transactions
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Transaction Date</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Customer</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Quantity Sold</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Unit Price</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentSalesTransactions as $transaction)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($transaction->sale_date)->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($transaction->sale_date)->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-medium">
                                        {{ $transaction->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                            {{ $transaction->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-900 font-semibold">
                                        ₱{{ number_format($transaction->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-indigo-600">
                                        ₱{{ number_format($transaction->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif($selectedProduct)
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <div class="text-center text-gray-500">
                <i class="fas fa-receipt text-4xl mb-3 block"></i>
                <p class="text-lg font-medium">No sales transactions found</p>
                <p class="text-sm">This product hasn't been sold yet at this location</p>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('locationFilter').addEventListener('change', function() {
        const location = this.value;
        const product = document.getElementById('productFilter').value;
        const url = new URL(window.location.href);
        url.searchParams.set('location', location);
        if (product) url.searchParams.set('product', product);
        window.location.href = url.toString();
    });

    document.getElementById('productFilter').addEventListener('change', function() {
        const product = this.value;
        const location = document.getElementById('locationFilter').value;
        const url = new URL(window.location.href);
        if (location) url.searchParams.set('location', location);
        url.searchParams.set('product', product);
        window.location.href = url.toString();
    });
</script>
@endsection
