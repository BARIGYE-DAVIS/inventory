@extends('layouts.cashier-layout')

@section('title', 'My Performance')
@section('page-title')
    <i class="fas fa-chart-line text-indigo-600 mr-2"></i>My Performance Dashboard
@endsection

@section('content')
<div class="space-y-6">

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Today -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Today's Performance</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['today']['sales'] }}</p>
                    <p class="text-green-100 text-xs mt-1">UGX {{ number_format($stats['today']['revenue'], 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-day text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Week -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">This Week</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['week']['sales'] }}</p>
                    <p class="text-blue-100 text-xs mt-1">UGX {{ number_format($stats['week']['revenue'], 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-week text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Month -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">This Month</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['month']['sales'] }}</p>
                    <p class="text-purple-100 text-xs mt-1">UGX {{ number_format($stats['month']['revenue'], 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- All Time -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">All Time</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['allTime']['sales'] }}</p>
                    <p class="text-yellow-100 text-xs mt-1">UGX {{ number_format($stats['allTime']['revenue'], 0) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-trophy text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-file-alt text-indigo-600 mr-2"></i>Quick Reports
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('cashier.performance.daily') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition border-2 border-green-200">
                <i class="fas fa-calendar-day text-3xl text-green-600 mr-4"></i>
                <div>
                    <p class="font-bold text-green-900">Daily Report</p>
                    <p class="text-xs text-green-700">Today's detailed breakdown</p>
                </div>
            </a>
            <a href="{{ route('cashier.performance.weekly') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition border-2 border-blue-200">
                <i class="fas fa-calendar-week text-3xl text-blue-600 mr-4"></i>
                <div>
                    <p class="font-bold text-blue-900">Weekly Report</p>
                    <p class="text-xs text-blue-700">This week's summary</p>
                </div>
            </a>
            <a href="{{ route('cashier.performance.monthly') }}" 
               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition border-2 border-purple-200">
                <i class="fas fa-calendar-alt text-3xl text-purple-600 mr-4"></i>
                <div>
                    <p class="font-bold text-purple-900">Monthly Report</p>
                    <p class="text-xs text-purple-700">This month's analysis</p>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- ✅ SALES TREND CHART (Line Chart) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                Sales Trend (Last 30 Days)
            </h3>
            <canvas id="trendChart" height="120"></canvas>
        </div>

        <!-- ✅ HOURLY PERFORMANCE (Bar Chart) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-clock text-green-600 mr-2"></i>
                Hourly Performance (Last 7 Days)
            </h3>
            <canvas id="hourlyChart" height="120"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- ✅ PAYMENT METHOD BREAKDOWN (Doughnut Chart) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                Payment Methods (This Month)
            </h3>
            <canvas id="paymentChart" height="150"></canvas>
        </div>

        <!-- ✅ TOP PRODUCTS (Bar Chart) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-box text-orange-600 mr-2"></i>
                Top 10 Products Sold (This Month)
            </h3>
            <canvas id="productsChart" height="150"></canvas>
        </div>
    </div>

    <!-- Weekly Comparison -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
            Weekly Comparison
        </h3>
        <canvas id="weeklyChart" height="80"></canvas>
    </div>

    <!-- Cashier Ranking -->
    @if($myPosition)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-trophy text-yellow-600 mr-2"></i>
            Cashier Leaderboard (This Month)
            <span class="text-sm font-normal text-gray-600">- You're ranked #{{ $myPosition }}</span>
        </h3>
        <div class="space-y-2">
            @foreach($rankings->take(10) as $index => $cashier)
            <div class="flex items-center justify-between p-3 rounded-lg {{ $cashier->id === auth()->id() ? 'bg-yellow-100 border-2 border-yellow-400' : 'bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">
                        @if($index === 0) 🥇
                        @elseif($index === 1) 🥈
                        @elseif($index === 2) 🥉
                        @else {{ $index + 1 }}
                        @endif
                    </span>
                    <div>
                        <p class="font-semibold {{ $cashier->id === auth()->id() ? 'text-yellow-900' : 'text-gray-800' }}">
                            {{ $cashier->name }}
                            @if($cashier->id === auth()->id()) <span class="text-xs">(You)</span> @endif
                        </p>
                        <p class="text-xs text-gray-500">{{ $cashier->sales_count }} sales</p>
                    </div>
                </div>
                <p class="font-bold text-green-600">UGX {{ number_format($cashier->revenue, 0) }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // ✅ SALES TREND LINE CHART
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendData = json($trendData);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'Revenue (UGX)',
                data: trendData.map(d => d.revenue),
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // ✅ HOURLY PERFORMANCE BAR CHART
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyData = json($hourlyPerformance);
    
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hourlyData.map(d => d.hour + ':00'),
            datasets: [{
                label: 'Average Sale',
                data: hourlyData.map(d => d.avg_sale),
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // ✅ PAYMENT METHOD DOUGHNUT CHART
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    const paymentData = json($paymentMethods);
    
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(d => d.payment_method.replace('_', ' ').toUpperCase()),
            datasets: [{
                data: paymentData.map(d => d.total),
                backgroundColor: [
                    'rgba(34, 197, 94, 0.7)',
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(168, 85, 247, 0.7)',
                    'rgba(249, 115, 22, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // ✅ TOP PRODUCTS HORIZONTAL BAR CHART
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    const productsData = json($topProducts);
    
    new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: productsData.map(d => d.name),
            datasets: [{
                label: 'Revenue',
                data: productsData.map(d => d.revenue),
                backgroundColor: 'rgba(249, 115, 22, 0.7)',
                borderColor: 'rgb(249, 115, 22)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // ✅ WEEKLY COMPARISON CHART
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyData = json($weeklyComparison);
    
    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: weeklyData.map(d => d.week),
            datasets: [{
                label: 'Revenue',
                data: weeklyData.map(d => d.revenue),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush