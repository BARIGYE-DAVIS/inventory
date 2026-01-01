@extends('layouts.app')

@section('title', 'Inventory Activities')

@section('page-title')
    <i class="fas fa-history text-indigo-600 mr-2"></i>Inventory Activities & Accounting
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <!-- Opening Stock -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Opening Stock</p>
                    <p class="text-2xl md:text-3xl font-bold text-blue-600 mt-2">{{ number_format($totals['opening_stock'], 0) }}</p>
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
                    <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($totals['purchases'], 0) }}</p>
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
                    <p class="text-2xl md:text-3xl font-bold text-orange-600 mt-2">{{ number_format($totals['sales'], 0) }}</p>
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
                    <p class="text-2xl md:text-3xl font-bold text-purple-600 mt-2">{{ number_format($totals['current_stock'], 0) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-boxes text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Value Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Opening Value -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Opening Value</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2">UGX {{ number_format($totals['opening_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-blue-300 text-3xl"></i>
            </div>
        </div>

        <!-- Purchases Value -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Purchases Value</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">UGX {{ number_format($totals['purchases_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-green-300 text-3xl"></i>
            </div>
        </div>
        
        <!-- Sales Value (Revenue) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Sales Value</p>
                    <p class="text-2xl font-bold text-orange-600 mt-2">UGX {{ number_format($totals['sales_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-orange-300 text-3xl"></i>
            </div>
        </div>

        <!-- Current Value -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Current Stock Value</p>
                    <p class="text-2xl font-bold text-purple-600 mt-2">UGX {{ number_format($totals['current_value'], 0) }}</p>
                </div>
                <i class="fas fa-coins text-purple-300 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Detailed Inventory Activities Table -->
    <div class="bg-white rounded-xl shadow-lg p-6 overflow-x-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Product Stock Movements</h2>
        
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-300 bg-gray-50">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Category</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-blue-600">Opening Stock</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-green-600">Purchases</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-orange-600">Sales</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-purple-600">Current Stock</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Cost Price</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Selling Price</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-purple-600">Stock Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($inventoryActivities as $activity)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <span class="font-semibold text-gray-900">{{ $activity['product_name'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                        {{ $activity['category'] }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">
                            {{ number_format($activity['opening_stock'], 0) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">
                            {{ number_format($activity['purchases'], 0) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-sm font-semibold">
                            {{ number_format($activity['sales'], 0) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm font-semibold">
                            {{ number_format($activity['current_stock'], 0) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                        UGX {{ number_format($activity['cost_price'], 0) }}
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                        UGX {{ number_format($activity['selling_price'], 0) }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm font-semibold">
                            UGX {{ number_format($activity['current_value'], 0) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        No products found in inventory
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t-2 border-gray-300 bg-gray-50 font-bold text-gray-900">
                <tr>
                    <td colspan="2" class="px-4 py-4">TOTAL</td>
                    <td class="px-4 py-4 text-center text-blue-600">
                        {{ number_format($totals['opening_stock'], 0) }}
                    </td>
                    <td class="px-4 py-4 text-center text-green-600">
                        {{ number_format($totals['purchases'], 0) }}
                    </td>
                    <td class="px-4 py-4 text-center text-orange-600">
                        {{ number_format($totals['sales'], 0) }}
                    </td>
                    <td class="px-4 py-4 text-center text-purple-600">
                        {{ number_format($totals['current_stock'], 0) }}
                    </td>
                    <td colspan="2" class="px-4 py-4 text-center"></td>
                    <td class="px-4 py-4 text-right text-purple-600">
                        UGX {{ number_format($totals['current_value'], 0) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Legend -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>Stock Calculation Legend
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Opening Stock:</span> Initial stock at the beginning of the period
                </p>
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Purchases:</span> Total quantity purchased/received from suppliers
                </p>
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Sales:</span> Total quantity sold to customers
                </p>
            </div>
            <div class="space-y-2">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Current Stock:</span> Opening + Purchases - Sales
                </p>
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Stock Value:</span> Current Stock × Cost Price (accounting value)
                </p>
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Sales Value:</span> Total Sales × Selling Price (revenue)
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
