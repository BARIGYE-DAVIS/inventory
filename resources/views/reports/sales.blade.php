@extends('layouts.app')

@section('title', 'Sales Report')

@section('page-title')
    <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Sales Report
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-filter text-indigo-600 mr-2"></i>Filter Report
        </h3>

        <form method="GET" action="{{ route('reports.sales') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ request('end_date', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Customer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Sales -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Sales</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalSales }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-4">
                    <i class="fas fa-shopping-cart text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">UGX {{ number_format($totalRevenue, 0) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Discount -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Discount</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">UGX {{ number_format($totalDiscount, 0) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-tags text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Sale -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Average Sale</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">UGX {{ number_format($averageSale, 0) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-chart-bar text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Sales Transactions</h3>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-print mr-2"></i>Print Report
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-indigo-600">
                            <a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $sale->sale_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $sale->customer->name ?? 'Walk-in' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $sale->items->count() }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">
                            UGX {{ number_format($sale->subtotal, 0) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-red-600">
                            UGX {{ number_format($sale->discount_amount, 0) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">
                            UGX {{ number_format($sale->tax_amount, 0) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                            UGX {{ number_format($sale->total, 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            No sales found for selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right">TOTALS:</td>
                        <td class="px-4 py-3 text-right">UGX {{ number_format($sales->sum('subtotal'), 0) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">UGX {{ number_format($sales->sum('discount_amount'), 0) }}</td>
                        <td class="px-4 py-3 text-right">UGX {{ number_format($sales->sum('tax_amount'), 0) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">UGX {{ number_format($sales->sum('total'), 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $sales->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection