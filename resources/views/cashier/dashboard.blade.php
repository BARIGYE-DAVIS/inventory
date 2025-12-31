<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - {{ auth()->user()->business->name }}</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script>-->

     @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar.hidden-mobile {
                transform: translateX(-100%);
            }
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        
        <!-- ========================================
             CASHIER SIDEBAR - SIMPLIFIED
        ======================================== -->
        <aside id="sidebar" class="sidebar fixed md:relative z-20 w-64 bg-indigo-900 text-white overflow-y-auto h-screen">
            <!-- Logo -->
            <div class="p-4 border-b border-yellow-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-cash-register text-2xl text-yellow-300"></i>
                        <span class="ml-2 text-lg font-bold">{{ auth()->user()->business->name }}</span>
                    </div>
                    <button onclick="toggleSidebar()" class="md:hidden text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p class="text-xs text-yellow-200 mt-1">💰 Cashier Dashboard</p>
            </div>
            <a href="{{ route('profit.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-800">
    <i class="fas fa-chart-pie text-lg"></i>
    <span>My Profit</span>
</a>
            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('cashier.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg bg-yellow-800 text-white">
                    <i class="fas fa-home text-lg"></i>
                    <span>My Dashboard</span>
                </a>

                <!-- NEW SALE (BIGGEST BUTTON) -->
                <a href="{{ route('pos.index') }}" class="flex items-center justify-center space-x-3 p-4 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-lg shadow-lg">
                    <i class="fas fa-plus-circle text-2xl"></i>
                    <span>NEW SALE</span>
                </a>
            <!-- ... existing sidebar code ... -->

<li>
    <a href="{{ route('cashier.invoices.index') }}"
       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-indigo-600 hover:text-white transition group {{ request()->routeIs('cashier.invoices.*') ? 'bg-indigo-100 text-indigo-700 font-bold' : '' }}">
        <i class="fas fa-file-invoice-dollar text-indigo-500 group-hover:text-white w-5"></i>
        <span class="ml-3">Invoices</span>
    </a>
</li>

<!-- ... further sidebar code ... -->
                <!-- My Sales -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-3 p-3 text-yellow-200 font-semibold">
                        <i class="fas fa-receipt text-lg"></i>
                        <span>My Sales</span>
                    </div>
                    <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-yellow-800">
                        <i class="fas fa-list text-sm"></i>
                        <span>All My Sales</span>
                    </a>
                    <a href="{{ route('sales.today') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-yellow-800">
                        <i class="fas fa-calendar-day text-sm"></i>
                        <span>Today</span>
                    </a>
                </div>

                <!-- Customers -->
                <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-800">
                    <i class="fas fa-users text-lg"></i>
                    <span>Customers</span>
                </a>

                <!-- Search Product -->
                <a href="{{ route('products.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-800">
                    <i class="fas fa-search text-lg"></i>
                    <span>Search Product</span>
                </a>

                <!-- My Profile -->
                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-800">
                    <i class="fas fa-user-circle text-lg"></i>
                    <span>My Profile</span>
                </a>
            </nav>

            <!-- User Profile & Logout -->
            <div class="p-4 border-t border-yellow-800 mt-auto">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-yellow-800 rounded-full flex items-center justify-center">
                        <span class="text-lg font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-yellow-200">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 p-2 bg-red-600 rounded-lg hover:bg-red-700">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ========================================
             MAIN CONTENT AREA
        ======================================== -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between p-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                        <i class="fas fa-chart-line text-yellow-600 mr-2"></i>My Performance Dashboard
                    </h1>
                    <div class="flex items-center space-x-4">
                        <span class="hidden md:inline text-sm text-gray-600" id="currentDateTime">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ now()->format('D, M d, Y h:i A') }}
                        </span>
                        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow">
                            <i class="fas fa-cash-register mr-1"></i>
                            <span class="hidden md:inline">NEW SALE</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded animate-pulse">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded animate-pulse">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
                @endif

                <!-- Welcome Message -->
                <div class="bg-indigo-900 rounded-xl shadow-lg p-6 text-white mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">
                                Hello, {{ auth()->user()->name }}! 👋
                            </h2>
                            <p class="mt-2">Here's your performance summary. Keep up the great work!</p>
                            @if($myPosition)
                            <p class="mt-2 text-yellow-100">
                                🏆 You're ranked #{{ $myPosition }} out of {{ $totalCashiers }} cashiers this month!
                            </p>
                            @endif
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-trophy text-5xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     MY PERFORMANCE STATS
                ======================================== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                    <!-- Today's Performance -->
                    <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">My Sales Today</p>
                                <p class="text-3xl font-bold mt-2">{{ $mySalesToday }}</p>
                                <p class="text-green-100 text-xs mt-1">UGX {{ number_format($myRevenueToday, 0) }}</p>
                                @if($mySalesToday > 0)
                                <p class="text-green-100 text-xs">Avg: UGX {{ number_format($myAvgSaleToday, 0) }}</p>
                                @endif
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-calendar-day text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- This Week -->
                    <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">This Week</p>
                                <p class="text-3xl font-bold mt-2">{{ $mySalesWeek }}</p>
                                <p class="text-blue-100 text-xs mt-1">UGX {{ number_format($myRevenueWeek, 0) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-calendar-week text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- This Month -->
                    <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm">This Month</p>
                                <p class="text-3xl font-bold mt-2">{{ $mySalesMonth }}</p>
                                <p class="text-purple-100 text-xs mt-1">UGX {{ number_format($myRevenueMonth, 0) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-calendar-alt text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- All Time -->
                    <div class="stat-card bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm">All Time</p>
                                <p class="text-3xl font-bold mt-2">{{ $myTotalSales }}</p>
                                <p class="text-yellow-100 text-xs mt-1">UGX {{ number_format($myTotalRevenue, 0) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-trophy text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     QUICK ACTIONS (LARGE BUTTONS)
                ======================================== -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-bolt text-yellow-600 mr-2"></i>Quick Actions
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('pos.index') }}" class="flex flex-col items-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition border-2 border-green-200 group">
                            <i class="fas fa-cash-register text-4xl text-green-600 mb-2 group-hover:scale-110 transition"></i>
                            <span class="text-sm font-bold text-green-900">NEW SALE</span>
                        </a>
                        <a href="{{ route('sales.index') }}" class="flex flex-col items-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition border-2 border-blue-200 group">
                            <i class="fas fa-list text-4xl text-blue-600 mb-2 group-hover:scale-110 transition"></i>
                            <span class="text-sm font-medium text-blue-900">My Sales</span>
                        </a>
                        <a href="{{ route('customers.create') }}" class="flex flex-col items-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 transition border-2 border-purple-200 group">
                            <i class="fas fa-user-plus text-4xl text-purple-600 mb-2 group-hover:scale-110 transition"></i>
                            <span class="text-sm font-medium text-purple-900">Add Customer</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="flex flex-col items-center p-6 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition border-2 border-yellow-200 group">
                            <i class="fas fa-search text-4xl text-yellow-600 mb-2 group-hover:scale-110 transition"></i>
                            <span class="text-sm font-medium text-yellow-900">Find Product</span>
                        </a>
                    </div>
                </div>

                <!-- ========================================
                     CHARTS SECTION
                ======================================== -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    
                    <!-- Daily Sales Trend (Last 7 Days) -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                            Daily Sales Trend (Last 7 Days)
                        </h3>
                        <canvas id="dailyTrendChart" height="120"></canvas>
                    </div>

                    <!-- Hourly Performance (Today) -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-clock text-green-600 mr-2"></i>
                            Hourly Performance (Today)
                        </h3>
                        <canvas id="hourlyChart" height="120"></canvas>
                    </div>
                </div>

                <!-- Monthly Overview Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                        Monthly Sales Overview (Last 6 Months)
                    </h3>
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    
                    <!-- My Recent Sales -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                <i class="fas fa-receipt text-yellow-600 mr-2"></i>
                                My Recent Sales
                            </h3>
                            <a href="{{ route('sales.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                View All <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($myRecentSales as $sale)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="window.location='{{ route('sales.show', $sale) }}'">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $sale->sale_number }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $sale->sale_date->format('M d, h:i A') }}
                                    </p>
                                    <p class="text-xs text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">UGX {{ number_format($sale->total, 0) }}</p>
                                    <span class="text-xs text-indigo-600">
                                        View <i class="fas fa-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">No sales yet today</p>
                                <a href="{{ route('pos.index') }}" class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-plus-circle mr-1"></i>Make Your First Sale
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Products I Sold Today -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                <i class="fas fa-box text-yellow-600 mr-2"></i>
                                Top Products Sold Today
                            </h3>
                            <a href="{{ route('sales.today') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($myTopProducts as $product)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded flex items-center justify-center">
                                        <i class="fas fa-box text-indigo-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-shopping-cart mr-1"></i>{{ $product->times_sold }} times
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-gray-700">UGX {{ number_format($product->selling_price, 0) }}</p>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <i class="fas fa-box-open text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">No products sold today yet</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Cashier Leaderboard 
                @if($cashierRankings->count() > 1)
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                        Cashier Leaderboard (This Month)
                    </h3>
                    <div class="space-y-2">
                        @foreach($cashierRankings as $index => $cashier)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $cashier->id === auth()->id() ? 'bg-yellow-100 border-2 border-yellow-400' : 'bg-gray-50' }} hover:shadow transition">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl font-bold">
                                    @if($index === 0) 🥇
                                    @elseif($index === 1) 🥈
                                    @elseif($index === 2) 🥉
                                    @else <span class="text-gray-500">{{ $index + 1 }}</span>
                                    @endif
                                </span>
                                <div>
                                    <p class="font-semibold {{ $cashier->id === auth()->id() ? 'text-yellow-900' : 'text-gray-800' }}">
                                        {{ $cashier->name }}
                                        @if($cashier->id === auth()->id()) 
                                        <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded ml-1">You</span> 
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-shopping-cart mr-1"></i>{{ $cashier->sales_count }} sales
                                    </p>
                                </div>
                            </div>
                            <p class="font-bold text-green-600">UGX {{ number_format($cashier->revenue, 0) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif -->

                <!-- Keyboard Shortcuts -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-keyboard text-indigo-600 mr-2"></i>
                        Keyboard Shortcuts & Tips
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white p-3 rounded-lg">
                            <p class="text-xs text-gray-500">Search Products</p>
                            <p class="font-bold text-indigo-600 text-lg">F2</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg">
                            <p class="text-xs text-gray-500">Clear Cart</p>
                            <p class="font-bold text-indigo-600 text-lg">F9</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg">
                            <p class="text-xs text-gray-500">Complete Sale</p>
                            <p class="font-bold text-indigo-600 text-lg">F12</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg">
                            <p class="text-xs text-gray-500">Barcode Scanner</p>
                            <p class="font-bold text-green-600 text-lg">Ready</p>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden-mobile');
        }

        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = event.target.closest('button');
            
            if (window.innerWidth < 768 && !sidebar.contains(event.target) && !toggleBtn) {
                sidebar.classList.add('hidden-mobile');
            }
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Update current time every second
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            document.getElementById('currentDateTime').innerHTML = 
                '<i class="fas fa-calendar mr-1"></i>' + now.toLocaleString('en-US', options);
        }
        setInterval(updateDateTime, 1000);

        // ========================================
        // CHART.JS CHARTS
        // ========================================

        // Daily Trend Chart (Last 7 Days)
        const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: [@for($i = 6; $i >= 0; $i--) '{{ now()->subDays($i)->format("D, M d") }}', @endfor],
                datasets: [{
                    label: 'Sales (UGX)',
                    data: [
                        @for($i = 6; $i >= 0; $i--)
                        {{ \App\Models\Sale::where('user_id', auth()->id())->whereDate('sale_date', now()->subDays($i))->sum('total') }},
                        @endfor
                    ],
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }, {
                    label: 'Count',
                    data: [
                        @for($i = 6; $i >= 0; $i--)
                        {{ \App\Models\Sale::where('user_id', auth()->id())->whereDate('sale_date', now()->subDays($i))->count() }},
                        @endfor
                    ],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1',
                    pointRadius: 5,
                    pointHoverRadius: 7
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
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return 'UGX ' + value.toLocaleString();
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Hourly Performance Chart (Today)
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: ['8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM'],
                datasets: [{
                    label: 'Sales (UGX)',
                    data: [
                        @for($hour = 8; $hour <= 18; $hour++)
                        {{ \App\Models\Sale::where('user_id', auth()->id())->whereDate('sale_date', today())->whereRaw('HOUR(sale_date) = ?', [$hour])->sum('total') }},
                        @endfor
                    ],
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
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

        // Monthly Overview Chart (Last 6 Months)
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: [
                    @for($i = 5; $i >= 0; $i--) 
                    '{{ now()->subMonths($i)->format("M Y") }}', 
                    @endfor
                ],
                datasets: [{
                    label: 'Revenue (UGX)',
                    data: [
                        @for($i = 5; $i >= 0; $i--)
                        {{ \App\Models\Sale::where('user_id', auth()->id())->whereYear('sale_date', now()->subMonths($i)->year)->whereMonth('sale_date', now()->subMonths($i)->month)->sum('total') }},
                        @endfor
                    ],
                    backgroundColor: 'rgba(168, 85, 247, 0.7)',
                    borderColor: 'rgb(168, 85, 247)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
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
</body>
</html>