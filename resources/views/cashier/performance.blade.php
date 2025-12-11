@extends('layouts.cashier-layout')

@section('title', 'My Performance')

@section('page-title')
    <i class="fas fa-chart-line text-green-600 mr-2"></i>My Performance
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Date Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('cashier.performance') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ $startDate }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ $endDate }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div class="self-end">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
            <div class="self-end">
                <a href="{{ route('cashier.performance') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Overall Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Sales</p>
                    <p class="text-4xl font-bold mt-2">{{ $totalSales }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-shopping-cart text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold mt-2">UGX {{ number_format($totalRevenue, 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Items Sold</p>
                    <p class="text-4xl font-bold mt-2">{{ $totalItems }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-box text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Average Sale</p>
                    <p class="text-xl font-bold mt-2">UGX {{ number_format($averageSale, 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-chart-bar text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Performance -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar-day text-green-600 mr-2"></i>Today's Performance
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                    <span class="text-gray-700 font-semibold">Sales Today:</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $todaySales }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                    <span class="text-gray-700 font-semibold">Revenue Today:</span>
                    <span class="text-xl font-bold text-green-600">UGX {{ number_format($todayRevenue, 0) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-line text-green-600 mr-2"></i>Weekly Comparison
            </h3>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700 font-semibold">Sales:</span>
                        <span class="text-xl font-bold">{{ $thisWeekSales }} vs {{ $lastWeekSales }}</span>
                    </div>
                    <div class="flex items-center">
                        @if($salesChange > 0)
                        <i class="fas fa-arrow-up text-green-600 mr-2"></i>
                        <span class="text-green-600 font-semibold">+{{ number_format($salesChange, 1) }}%</span>
                        @elseif($salesChange < 0)
                        <i class="fas fa-arrow-down text-red-600 mr-2"></i>
                        <span class="text-red-600 font-semibold">{{ number_format($salesChange, 1) }}%</span>
                        @else
                        <span class="text-gray-600">No change</span>
                        @endif
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700 font-semibold">Revenue:</span>
                        <span class="text-sm font-bold">UGX {{ number_format($thisWeekRevenue, 0) }}</span>
                    </div>
                    <div class="flex items-center">
                        @if($revenueChange > 0)
                        <i class="fas fa-arrow-up text-green-600 mr-2"></i>
                        <span class="text-green-600 font-semibold">+{{ number_format($revenueChange, 1) }}%</span>
                        @elseif($revenueChange < 0)
                        <i class="fas fa-arrow-down text-red-600 mr-2"></i>
                        <span class="text-red-600 font-semibold">{{ number_format($revenueChange, 1) }}%</span>
                        @else
                        <span class="text-gray-600">No change</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Target Progress -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-bullseye text-green-600 mr-2"></i>Monthly Target Progress
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Target: {{ $monthlyTarget }} sales</span>
                <span class="font-semibold">Current: {{ $totalSales }} sales</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-6">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" 
                     style="width: {{ min($targetProgress, 100) }}%">
                    {{ number_format($targetProgress, 1) }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Breakdown Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-area text-green-600 mr-2"></i>Daily Performance
        </h3>
        <canvas id="dailyChart" height="80"></canvas>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-trophy text-green-600 mr-2"></i>Top Selling Products
        </h3>
        @if($topProducts->count() > 0)
        <div class="space-y-3">
            @foreach($topProducts as $index => $product)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <span class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">
                        {{ $index + 1 }}
                    </span>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->total_quantity }} units sold</p>
                    </div>
                </div>
                <span class="font-bold text-green-600">UGX {{ number_format($product->total_revenue, 0) }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-500 py-8">No sales data available</p>
        @endif
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-wallet text-green-600 mr-2"></i>Payment Methods Breakdown
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($paymentMethods as $method)
            <div class="border border-gray-200 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600 mb-2">{{ ucfirst(str_replace('_', ' ', $method->payment_method)) }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ $method->count }}</p>
                <p class="text-xs text-green-600 font-semibold mt-1">UGX {{ number_format($method->total, 0) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Performance Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyBreakdown->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode($dailyBreakdown->pluck('sales')) !!},
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                yAxisID: 'y',
            }, {
                label: 'Revenue (UGX)',
                data: {!! json_encode($dailyBreakdown->pluck('revenue')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                yAxisID: 'y1',
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sales'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue (UGX)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                },
            }
        }
    });
</script>
@endpush