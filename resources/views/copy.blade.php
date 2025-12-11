<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ auth()->user()->business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar.hidden-mobile {
                transform: translateX(-100%);
            }
        }
        
        /* ✅ CHART ANIMATIONS */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .chart-container {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        /* ✅ STAT CARD HOVER EFFECTS */
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* ✅ PROFIT BADGE COLORS */
        .profit-positive {
            color: #10b981;
        }
        
        .profit-negative {
            color: #ef4444;
        }

        /* ✅ PERIOD SELECTOR TABS */
        .period-tab {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .period-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        
        <!-- ========================================
             SIDEBAR
        ======================================== -->
        <aside id="sidebar" class="sidebar fixed md:relative z-20 w-64 bg-indigo-900 text-white overflow-y-auto h-screen">
            <!-- Logo -->
            <div class="p-4 border-b border-indigo-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-boxes text-2xl text-yellow-400"></i>
                        <span class="ml-2 text-lg font-bold">{{ auth()->user()->business->name }}</span>
                    </div>
                    <button onclick="toggleSidebar()" class="md:hidden text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p class="text-xs text-indigo-300 mt-1">{{ auth()->user()->role->display_name }}</p>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg bg-indigo-800 text-white">
                    <i class="fas fa-home text-lg"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Profit Report -->
                <a href="{{ route('profit.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-chart-pie text-lg"></i>
                    <span>Profit Report</span>
                </a>

                <!-- Annual Performance -->
                <a href="{{ route('dashboard.annual') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-chart-line mr-2"></i>
                    <span>Annual Performance</span>
                </a>

                <!-- POS -->
                <a href="{{ route('pos.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-cash-register text-lg text-green-400"></i>
                    <span>POS - New Sale</span>
                </a>

                <!-- Products - Admin, Manager, Owner only -->
                @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']))
                <div class="space-y-1">
                    <div class="flex items-center space-x-3 p-3 text-indigo-300 font-semibold">
                        <i class="fas fa-box text-lg"></i>
                        <span>Products</span>
                    </div>
                    <a href="{{ route('products.index') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-list text-sm"></i>
                        <span>All Products</span>
                    </a>
                    <a href="{{ route('products.create') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-plus text-sm"></i>
                        <span>Add Product</span>
                    </a>
                </div>
                @endif

                <!-- Sales -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-3 p-3 text-indigo-300 font-semibold">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span>Sales</span>
                    </div>
                    <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-list text-sm"></i>
                        <span>{{ in_array(auth()->user()->role->name, ['cashier', 'staff']) ? 'My Sales' : 'All Sales' }}</span>
                    </a>
                </div>

                @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']))
                <!-- Inventory -->
                <a href="{{ route('inventory.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-warehouse text-lg"></i>
                    <span>Inventory</span>
                </a>

                <!-- Suppliers -->
                <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-truck text-lg"></i>
                    <span>Suppliers</span>
                </a>

                <!-- Customers -->
                <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-users text-lg"></i>
                    <span>Customers</span>
                </a>
                @endif
            </nav>

            <!-- User Profile & Logout -->
            <div class="p-4 border-t border-indigo-800 mt-auto">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-indigo-700 rounded-full flex items-center justify-center">
                        <span class="text-lg font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-indigo-300">{{ auth()->user()->email }}</p>
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
                        <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Dashboard
                    </h1>
                    <div class="flex items-center space-x-4">
                        <span class="hidden md:inline text-sm text-gray-600">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ now()->format('D, M d, Y') }}
                        </span>
                        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-cash-register mr-1"></i>
                            <span class="hidden md:inline">POS</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
                @endif

                <!-- ========================================
                     ✅ PERIOD SELECTOR TABS
                ======================================== -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="changePeriod('day')" class="period-tab px-4 py-2 rounded-lg {{ $period == 'day' ? 'active' : 'bg-gray-100' }}">
                                <i class="fas fa-calendar-day mr-1"></i> Daily
                            </button>
                            <button onclick="changePeriod('week')" class="period-tab px-4 py-2 rounded-lg {{ $period == 'week' ? 'active' : 'bg-gray-100' }}">
                                <i class="fas fa-calendar-week mr-1"></i> Weekly
                            </button>
                            <button onclick="changePeriod('month')" class="period-tab px-4 py-2 rounded-lg {{ $period == 'month' ? 'active' : 'bg-gray-100' }}">
                                <i class="fas fa-calendar-alt mr-1"></i> Monthly
                            </button>
                            <button onclick="changePeriod('year')" class="period-tab px-4 py-2 rounded-lg {{ $period == 'year' ? 'active' : 'bg-gray-100' }}">
                                <i class="fas fa-calendar mr-1"></i> Yearly
                            </button>
                        </div>

                        <!-- ✅ YEAR SELECTOR (for monthly/yearly view) -->
                        @if($period == 'month' || $period == 'year')
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Year:</label>
                            <select onchange="changeYear(this.value)" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ========================================
                     ✅ KEY STATS CARDS WITH PROFIT
                ======================================== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                    <!-- Today's Sales -->
                    <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-semibold">Today's Sales</p>
                                <p class="text-3xl font-bold mt-2">UGX {{ number_format($todayRevenue, 0) }}</p>
                                <p class="text-green-100 text-xs mt-1">{{ $todaySales }} transactions</p>
                                <div class="mt-2 pt-2 border-t border-green-400">
                                    <p class="text-xs text-green-100">Profit: <span class="font-bold">UGX {{ number_format($todayGrossProfit, 0) }}</span></p>
                                    <p class="text-xs text-green-100">Margin: <span class="font-bold">{{ number_format($todayProfitMargin, 1) }}%</span></p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-money-bill-wave text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- This Week -->
                    <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-semibold">This Week</p>
                                <p class="text-3xl font-bold mt-2">UGX {{ number_format($weekRevenue, 0) }}</p>
                                <p class="text-blue-100 text-xs mt-1">{{ $weekSales }} transactions</p>
                                <div class="mt-2 pt-2 border-t border-blue-400">
                                    <p class="text-xs text-blue-100">Profit: <span class="font-bold">UGX {{ number_format($weekGrossProfit, 0) }}</span></p>
                                    <p class="text-xs text-blue-100">Margin: <span class="font-bold">{{ number_format($weekProfitMargin, 1) }}%</span></p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-calendar-week text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- This Month -->
                    <div class="stat-card bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-semibold">This Month</p>
                                <p class="text-3xl font-bold mt-2">UGX {{ number_format($monthRevenue, 0) }}</p>
                                <p class="text-yellow-100 text-xs mt-1">{{ $monthSales }} transactions</p>
                                <div class="mt-2 pt-2 border-t border-yellow-400">
                                    <p class="text-xs text-yellow-100">Profit: <span class="font-bold">UGX {{ number_format($monthGrossProfit, 0) }}</span></p>
                                    <p class="text-xs text-yellow-100">Margin: <span class="font-bold">{{ number_format($monthProfitMargin, 1) }}%</span></p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-calendar-alt text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- All Time -->
                    <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-semibold">All Time</p>
                                <p class="text-3xl font-bold mt-2">UGX {{ number_format($totalRevenue, 0) }}</p>
                                <p class="text-purple-100 text-xs mt-1">{{ $totalSales }} transactions</p>
                                <div class="mt-2 pt-2 border-t border-purple-400">
                                    <p class="text-xs text-purple-100">Profit: <span class="font-bold">UGX {{ number_format($totalGrossProfit, 0) }}</span></p>
                                    <p class="text-xs text-purple-100">Margin: <span class="font-bold">{{ number_format($totalProfitMargin, 1) }}%</span></p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-full p-4">
                                <i class="fas fa-infinity text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                                <!-- ========================================
                     ✅ LOW STOCK ALERT (Admin, Manager, Owner only)
                ======================================== -->
                @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']) && $lowStockProducts && $lowStockProducts->count() > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-xl shadow-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Low Stock Alert ({{ $lowStockProducts->count() }} products)
                        </h3>
                        <a href="{{ route('inventory.index') }}" class="text-yellow-700 hover:text-yellow-900 font-semibold text-sm">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        @foreach($lowStockProducts->take(5) as $product)
                        <div class="bg-white rounded-lg p-3 border border-yellow-200">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded mb-2">
                            <p class="font-semibold text-sm text-gray-900 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-red-600 font-bold">Only {{ $product->quantity }} left!</p>
                            <p class="text-xs text-gray-500">Reorder at: {{ $product->reorder_level }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- ========================================
                     ✅ PROFIT VS REVENUE CHART (MAIN)
                ======================================== -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6 chart-container">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-chart-area text-indigo-600 mr-2"></i>
                            Revenue vs Profit Analysis
                        </h3>
                        <div class="flex gap-2">
                            <span class="text-xs px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full">
                                <i class="fas fa-circle text-indigo-600 mr-1"></i> Revenue
                            </span>
                            <span class="text-xs px-3 py-1 bg-green-100 text-green-800 rounded-full">
                                <i class="fas fa-circle text-green-600 mr-1"></i> Profit
                            </span>
                            <span class="text-xs px-3 py-1 bg-red-100 text-red-800 rounded-full">
                                <i class="fas fa-circle text-red-600 mr-1"></i> COGS
                            </span>
                        </div>
                    </div>
                    <canvas id="profitRevenueChart" height="80"></canvas>
                </div>

                <!-- ========================================
                     ✅ CHARTS ROW - 2 COLUMNS
                ======================================== -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    
                    <!-- ✅ PROFIT MARGIN TREND -->
                    <div class="bg-white rounded-xl shadow-lg p-6 chart-container">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-percentage text-green-600 mr-2"></i>Profit Margin Trend
                        </h3>
                        <canvas id="profitMarginChart" height="120"></canvas>
                    </div>

                    <!-- ✅ SALES BY HOUR (TODAY) -->
                    <div class="bg-white rounded-xl shadow-lg p-6 chart-container">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-clock text-blue-600 mr-2"></i>Sales by Hour (Today)
                        </h3>
                        <canvas id="hourlyChart" height="120"></canvas>
                    </div>
                </div>

                <!-- ========================================
                     ✅ PIE CHARTS ROW - 2 COLUMNS
                ======================================== -->
                @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    
                    <!-- ✅ SALES BY PAYMENT METHOD -->
                    <div class="bg-white rounded-xl shadow-lg p-6 chart-container">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-credit-card text-purple-600 mr-2"></i>Payment Methods
                        </h3>
                        <canvas id="paymentMethodChart" height="120"></canvas>
                    </div>

                    <!-- ✅ SALES BY CATEGORY -->
                    <div class="bg-white rounded-xl shadow-lg p-6 chart-container">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-tags text-orange-600 mr-2"></i>Sales by Category
                        </h3>
                        <canvas id="categoryChart" height="120"></canvas>
                    </div>
                </div>

                <!-- ========================================
                     ✅ PROFIT ANALYSIS SECTION
                ======================================== -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    
                    <!-- ✅ TOP PROFITABLE PRODUCTS -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                <i class="fas fa-trophy text-yellow-500 mr-2"></i>Most Profitable
                            </h3>
                            <span class="text-xs text-gray-500">This Month</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($topProfitableProducts->take(5) as $product)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-product-image.png') }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-10 h-10 rounded object-cover">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->units_sold }} sold</p>
                                        <p class="text-xs text-green-600 font-bold">
                                            Margin: {{ number_format($product->profit_margin, 1) }}%
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 text-sm">
                                        +UGX {{ number_format($product->profit, 0) }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4 text-sm">No data yet</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- ✅ TOP SELLING PRODUCTS (BY REVENUE) -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                <i class="fas fa-fire text-orange-500 mr-2"></i>Top Selling
                            </h3>
                            <span class="text-xs text-gray-500">This Month</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($topSellingProducts as $product)
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-product-image.png') }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-10 h-10 rounded object-cover">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->units_sold }} units sold</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-blue-600 text-sm">UGX {{ number_format($product->revenue, 0) }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4 text-sm">No sales data yet</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- ✅ LOSS-MAKING PRODUCTS (IF ANY) -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>Loss Makers
                            </h3>
                            <span class="text-xs text-gray-500">This Month</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($lossMakingProducts->take(5) as $product)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->units_sold }} sold</p>
                                        <p class="text-xs text-gray-600">
                                            Cost: UGX {{ number_format($product->cost_price, 0) }} | 
                                            Sold: UGX {{ number_format($product->avg_selling_price, 0) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-red-600 text-sm">
                                        -UGX {{ number_format(abs($product->loss), 0) }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                <p class="text-gray-500 text-sm">No loss-making products!</p>
                                <p class="text-xs text-gray-400">All products sold above cost</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- ✅ PROFIT BY CATEGORY (TABLE) -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-layer-group text-indigo-600 mr-2"></i>Profit by Category
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">COGS</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Margin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($profitByCategory as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-600">UGX {{ number_format($category->revenue, 0) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-600">UGX {{ number_format($category->total_cost, 0) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold {{ $category->profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        UGX {{ number_format($category->profit, 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                            {{ $category->profit_margin > 30 ? 'bg-green-100 text-green-800' : ($category->profit_margin > 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ number_format($category->profit_margin, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        No category data yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ✅ TOP STAFF PERFORMANCE -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-users text-indigo-600 mr-2"></i>Top Staff Performance (This Month)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sales</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($topStaffPerformance as $index => $staff)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">
                                        <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold
                                            {{ $index == 0 ? 'bg-yellow-100 text-yellow-800' : ($index == 1 ? 'bg-gray-100 text-gray-800' : ($index == 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-50 text-blue-800')) }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $staff->name }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $staff->sales_count }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                        UGX {{ number_format($staff->sales_sum_total, 0) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        No staff data yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- ========================================
                     CASHIER/STAFF VIEW
                ======================================== -->
                @if(in_array(auth()->user()->role->name, ['cashier', 'staff']))
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-user-chart text-indigo-600 mr-2"></i>My Performance
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Average Sale Today</span>
                            <span class="text-lg font-bold text-blue-600">UGX {{ number_format($todayAvgSale, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Total Transactions</span>
                            <span class="text-lg font-bold text-green-600">{{ $totalSales }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Total Revenue</span>
                            <span class="text-lg font-bold text-purple-600">UGX {{ number_format($totalRevenue, 0) }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- ========================================
                     ✅ QUICK ACTIONS
                ======================================== -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('pos.index') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                            <i class="fas fa-cash-register text-3xl text-green-600 mb-2"></i>
                            <span class="text-sm font-medium text-green-900">New Sale</span>
                        </a>
                        
                        @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']))
                        <a href="{{ route('products.create') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <i class="fas fa-plus-circle text-3xl text-blue-600 mb-2"></i>
                            <span class="text-sm font-medium text-blue-900">Add Product</span>
                        </a>
                        <a href="{{ route('customers.create') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                            <i class="fas fa-user-plus text-3xl text-purple-600 mb-2"></i>
                            <span class="text-sm font-medium text-purple-900">Add Customer</span>
                        </a>
                        <a href="{{ route('profit.index') }}" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                            <i class="fas fa-chart-pie text-3xl text-orange-600 mb-2"></i>
                            <span class="text-sm font-medium text-orange-900">Profit Report</span>
                        </a>
                        @else
                        <a href="{{ route('sales.index') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <i class="fas fa-list text-3xl text-blue-600 mb-2"></i>
                            <span class="text-sm font-medium text-blue-900">My Sales</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                            <i class="fas fa-user-cog text-3xl text-purple-600 mb-2"></i>
                            <span class="text-sm font-medium text-purple-900">Profile</span>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- ========================================
                     ✅ RECENT SALES TABLE
                ======================================== -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-receipt text-indigo-600 mr-2"></i>Recent Sales
                        </h3>
                        <a href="{{ route('sales.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']))
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                                    @endif
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentSales as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-indigo-600">
                                        <a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->sale_date->format('M d, Y h:i A') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                    @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']))
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->user->name }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-sm font-semibold text-green-600">UGX {{ number_format($sale->total, 0) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $sale->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($sale->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No sales recorded yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>



        <!-- ========================================
         ✅ JAVASCRIPT - ALL CHARTS & INTERACTIONS
    ======================================== -->
    <script>
        // ========================================
        // SIDEBAR TOGGLE
        // ========================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden-mobile');
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = event.target.closest('button');
            
            if (window.innerWidth < 768 && !sidebar.contains(event.target) && !toggleBtn) {
                sidebar.classList.add('hidden-mobile');
            }
        });

        // ========================================
        // PERIOD SELECTOR
        // ========================================
        function changePeriod(period) {
            const url = new URL(window.location.href);
            url.searchParams.set('period', period);
            window.location.href = url.toString();
        }

        function changeYear(year) {
            const url = new URL(window.location.href);
            url.searchParams.set('year', year);
            window.location.href = url.toString();
        }

        // ========================================
        // ✅ CHART.JS GLOBAL CONFIG
        // ========================================
        Chart.defaults.font.family = "'Inter', 'system-ui', 'sans-serif'";
        Chart.defaults.color = '#6B7280';
        Chart.defaults.plugins.legend.display = true;
        Chart.defaults.plugins.legend.position = 'bottom';

        // ========================================
        // ✅ 1. PROFIT VS REVENUE CHART (MAIN)
        // ========================================
        const profitRevenueCtx = document.getElementById('profitRevenueChart').getContext('2d');
        
        @php
            $period = request('period', 'week');
            if ($period == 'day') {
                $trendData = $profitTrend;
            } elseif ($period == 'week') {
                $trendData = $weeklyProfitTrend;
            } elseif ($period == 'month') {
                $trendData = $monthlyProfitTrend;
            } else {
                $trendData = $profitTrend;
            }
        @endphp

        const profitLabels = @json($trendData->pluck('label'));
        const revenueData = @json($trendData->pluck('revenue'));
        const cogsData = @json($trendData->pluck('cogs'));
        const profitData = @json($trendData->pluck('profit'));

        const profitRevenueChart = new Chart(profitRevenueCtx, {
            type: 'bar',
            data: {
                labels: profitLabels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: revenueData,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 2,
                        borderRadius: 8,
                        order: 3
                    },
                    {
                        label: 'COGS',
                        data: cogsData,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 2,
                        borderRadius: 8,
                        order: 2
                    },
                    {
                        label: 'Profit',
                        data: profitData,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 2,
                        borderRadius: 8,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'UGX ' + context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'UGX ' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // ========================================
        // ✅ 2. PROFIT MARGIN TREND CHART
        // ========================================
        const profitMarginCtx = document.getElementById('profitMarginChart').getContext('2d');
        
        const profitMarginData = @json($trendData->pluck('profit_margin'));

        const profitMarginChart = new Chart(profitMarginCtx, {
            type: 'line',
            data: {
                labels: profitLabels,
                datasets: [{
                    label: 'Profit Margin (%)',
                    data: profitMarginData,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'Margin: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // ========================================
        // ✅ 3. HOURLY SALES CHART (TODAY)
        // ========================================
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        
        const hourlyLabels = @json(collect($hourlyData)->pluck('hour')->map(function($h) { return $h . ':00'; }));
        const hourlyRevenue = @json(collect($hourlyData)->pluck('revenue'));

        const hourlyChart = new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Sales (UGX)',
                    data: hourlyRevenue,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'UGX ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'UGX ' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // ========================================
        // ✅ 4. PAYMENT METHOD PIE CHART
        // ========================================
        @if(in_array(auth()->user()->role->name, ['admin', 'manager', 'owner']))
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        
        const paymentMethods = @json($paymentMethodData->pluck('payment_method')->map(function($pm) { 
            return ucfirst(str_replace('_', ' ', $pm)); 
        }));
        const paymentTotals = @json($paymentMethodData->pluck('total'));

        const paymentColors = [
            'rgba(99, 102, 241, 0.8)',   // Indigo
            'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(251, 191, 36, 0.8)',   // Yellow
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(168, 85, 247, 0.8)'    // Purple
        ];

        const paymentMethodChart = new Chart(paymentMethodCtx, {
            type: 'doughnut',
            data: {
                labels: paymentMethods,
                datasets: [{
                    data: paymentTotals,
                    backgroundColor: paymentColors,
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': UGX ' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // ========================================
        // ✅ 5. SALES BY CATEGORY PIE CHART
        // ========================================
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        
        const categoryNames = @json($salesByCategory->pluck('name'));
        const categoryRevenue = @json($salesByCategory->pluck('revenue'));

        const categoryColors = [
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(251, 191, 36, 0.8)',   // Yellow
            'rgba(34, 197, 94, 0.8)',    // Green
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(168, 85, 247, 0.8)',   // Purple
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(20, 184, 166, 0.8)',   // Teal
            'rgba(249, 115, 22, 0.8)'    // Orange
        ];

        const categoryChart = new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: categoryNames,
                datasets: [{
                    data: categoryRevenue,
                    backgroundColor: categoryColors,
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': UGX ' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif

        // ========================================
        // AUTO-HIDE ALERTS
        // ========================================
        setTimeout(function() {
            const alerts = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // ========================================
        // ✅ CHART ANIMATIONS ON SCROLL
        // ========================================
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const chartObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeIn 0.6s ease-in-out';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.chart-container').forEach(chart => {
            chartObserver.observe(chart);
        });

        // ========================================
        // ✅ STAT CARD COUNTER ANIMATION
        // ========================================
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const value = Math.floor(progress * (end - start) + start);
                element.textContent = 'UGX ' + value.toLocaleString();
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Animate revenue numbers on page load
        window.addEventListener('load', () => {
            const revenueElements = document.querySelectorAll('.stat-card .text-3xl');
            revenueElements.forEach(el => {
                const text = el.textContent.replace(/[^0-9]/g, '');
                const finalValue = parseInt(text);
                if (finalValue > 0) {
                    el.textContent = 'UGX 0';
                    setTimeout(() => animateValue(el, 0, finalValue, 1500), 200);
                }
            });
        });

        // ========================================
        // ✅ PRINT FUNCTIONALITY (for future use)
        // ========================================
        function printDashboard() {
            window.print();
        }

        // ========================================
        // ✅ REFRESH DATA BUTTON
        // ========================================
        function refreshDashboard() {
            location.reload();
        }

        // ========================================
        // ✅ KEYBOARD SHORTCUTS
        // ========================================
        document.addEventListener('keydown', function(e) {
            // Ctrl+P or Cmd+P = Open POS
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.location.href = '{{ route("pos.index") }}';
            }
            
            // Ctrl+R or Cmd+R = Refresh (default browser behavior)
            // Ctrl+D or Cmd+D = Go to Dashboard
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                window.location.href = '{{ route("dashboard") }}';
            }
        });

        // ========================================
        // ✅ CONSOLE STATS (for debugging)
        // ========================================
        console.log('📊 Dashboard Loaded Successfully!');
        console.log('💰 Today\'s Revenue: UGX {{ number_format($todayRevenue, 0) }}');
        console.log('💵 Today\'s Profit: UGX {{ number_format($todayGrossProfit, 0) }}');
        console.log('📈 Profit Margin: {{ number_format($todayProfitMargin, 1) }}%');
        console.log('📅 Period: {{ $period }}');
        @if($period == 'month' || $period == 'year')
        console.log('🗓️ Selected Year: {{ $selectedYear }}');
        @endif
    </script>
</body>
</html>