@extends('layouts.app')

@section('title', 'Profit Report')

@section('content')
<div class="space-y-6">
    
    <h1 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Profit Report
    </h1>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('profit.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Cashier</label>
                <select name="cashier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Cashiers</option>
                    @foreach($cashiers as $cashier)
                        <option value="{{ $cashier->id }}" {{ $selectedCashierId == $cashier->id ? 'selected' : '' }}>
                            {{ $cashier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('profit.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold mt-2">UGX {{ number_format($totalRevenue, 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Total Cost</p>
                    <p class="text-2xl font-bold mt-2">UGX {{ number_format($totalCost, 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-receipt text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Profit</p>
                    <p class="text-2xl font-bold mt-2">UGX {{ number_format($totalProfit, 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Profit Margin</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($profitMargin, 1) }}%</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-percentage text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cashier Performance (Only show when viewing all cashiers) -->
    @if(!$selectedCashierId && $cashierPerformance->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-users text-indigo-600 mr-2"></i>Cashier Performance Comparison
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cashier</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sales</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cashierPerformance as $index => $cashier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-lg font-bold text-indigo-600">{{ substr($cashier->cashier_name, 0, 1) }}</span>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $cashier->cashier_name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">{{ $cashier->total_sales }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-blue-600">UGX {{ number_format($cashier->revenue, 0) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-red-600">UGX {{ number_format($cashier->cost, 0) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-green-600">UGX {{ number_format($cashier->profit, 0) }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('profit.index', ['cashier_id' => $cashier->user_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Daily Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>Daily Profit Breakdown
        </h3>
        <canvas id="dailyProfitChart" height="80"></canvas>
    </div>

    <!-- Top Selling by Quantity -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-fire text-orange-600 mr-2"></i>Top 10 Best Selling Products (By Quantity)
    </h3>
    
    @if($topSellingByQuantity->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty Sold</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Times Sold</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topSellingByQuantity as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">
                        <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center">
                            {{ $index + 1 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                        @if($item->product_sku)
                        <p class="text-xs text-gray-500">{{ $item->product_sku }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-lg font-bold text-orange-600">{{ number_format($item->total_quantity_sold, 0) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-gray-600">{{ $item->number_of_sales }} sales</td>
                    <td class="px-6 py-4 text-right font-semibold text-blue-600">UGX {{ number_format($item->total_revenue, 0) }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-red-600">UGX {{ number_format($item->total_cost, 0) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">UGX {{ number_format($item->profit, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-center text-gray-500 py-8">No sales data available</p>
    @endif
</div>

<!-- Top Selling by Revenue -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-dollar-sign text-green-600 mr-2"></i>Top 10 Best Selling Products (By Revenue)
    </h3>
    
    @if($topSellingByRevenue->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty Sold</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topSellingByRevenue as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">
                        <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 font-bold flex items-center justify-center">
                            {{ $index + 1 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                        @if($item->product_sku)
                        <p class="text-xs text-gray-500">{{ $item->product_sku }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-lg font-bold text-green-600">UGX {{ number_format($item->total_revenue, 0) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-gray-600">{{ number_format($item->total_quantity_sold, 0) }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-red-600">UGX {{ number_format($item->total_cost, 0) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-blue-600">UGX {{ number_format($item->profit, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-center text-gray-500 py-8">No sales data available</p>
    @endif
</div>

    <!-- Weekly & Monthly Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar-week text-indigo-600 mr-2"></i>Weekly Comparison
            </h3>
            <div class="space-y-3">
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">This Week</p>
                    <p class="text-2xl font-bold text-green-600">UGX {{ number_format($thisWeekProfit, 0) }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">Last Week</p>
                    <p class="text-2xl font-bold text-gray-600">UGX {{ number_format($lastWeekProfit, 0) }}</p>
                </div>
                <div class="p-4 border-t-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-semibold">Change:</span>
                        @if($weeklyProfitChange > 0)
                        <span class="text-green-600 font-bold">
                            <i class="fas fa-arrow-up mr-1"></i>+{{ number_format($weeklyProfitChange, 1) }}%
                        </span>
                        @elseif($weeklyProfitChange < 0)
                        <span class="text-red-600 font-bold">
                            <i class="fas fa-arrow-down mr-1"></i>{{ number_format($weeklyProfitChange, 1) }}%
                        </span>
                        @else
                        <span class="text-gray-600">No change</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar text-indigo-600 mr-2"></i>This Month Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-gray-700">Total Sales:</span>
                    <span class="font-bold text-blue-600">{{ $monthTotalSales }}</span>
                </div>
                <div class="flex justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-gray-700">Total Revenue:</span>
                    <span class="font-bold text-green-600">UGX {{ number_format($monthRevenue, 0) }}</span>
                </div>
                <div class="flex justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-gray-700">Total Cost:</span>
                    <span class="font-bold text-red-600">UGX {{ number_format($monthCost, 0) }}</span>
                </div>
                <div class="flex justify-between p-3 bg-purple-50 rounded-lg border-2 border-purple-200">
                    <span class="text-gray-700 font-bold">Net Profit:</span>
                    <span class="font-bold text-purple-600 text-xl">UGX {{ number_format($monthProfit, 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Margin Warning -->
    @if($lowMarginProducts->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6">
        <h3 class="text-lg font-bold text-yellow-800 mb-4">
            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>Low Profit Margin Products (Below 15%)
        </h3>
        <div class="space-y-2">
            @foreach($lowMarginProducts as $product)
            <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                <div>
                    <p class="font-semibold text-gray-900">{{ $product->product_name }}</p>
                    <p class="text-xs text-gray-500">Sold: {{ number_format($product->total_quantity, 0) }} units</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-yellow-600">{{ number_format($product->margin, 1) }}% margin</p>
                    <p class="text-sm text-gray-600">Profit: UGX {{ number_format($product->profit, 0) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('dailyProfitChart');
if (ctx) {
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailyProfit->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($dailyProfit->pluck('revenue')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 2
            }, {
                label: 'Cost',
                data: {!! json_encode($dailyProfit->pluck('cost')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 2
            }, {
                label: 'Profit',
                data: {!! json_encode($dailyProfit->pluck('profit')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'UGX ' + value.toLocaleString()
                    }
                }
            }
        }
    });
}
</script>
@endpush