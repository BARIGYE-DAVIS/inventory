@extends('layouts.app')

@section('title', 'Annual Performance Report')

@section('page-title')
    <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Annual Performance Report - {{ $selectedYear }}
@endsection

@section('content')
<!-- Year Selector & Actions -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
    <!-- Year Selector -->
    <div class="flex items-center space-x-4">
        <label class="text-gray-700 font-semibold">Select Year:</label>
        <select onchange="window.location.href='{{ route('dashboard.annual') }}?year=' + this.value" 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            @foreach($availableYears as $year)
                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-2">
        <!-- Print -->
        <button onclick="printReport()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-print mr-1"></i>Print
        </button>

        <!-- Export Excel -->
        <a href="{{ route('dashboard.annual.export') }}?year={{ $selectedYear }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-file-excel mr-1"></i>Excel
        </a>

        <!-- Share Dropdown -->
        <div class="relative share-dropdown">
            <button onclick="toggleShareDropdown()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-share-alt mr-1"></i>Share
            </button>
            <div id="shareDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                <a href="javascript:void(0)" onclick="shareWhatsApp()" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                    <i class="fab fa-whatsapp text-green-600 mr-2"></i>WhatsApp
                </a>
                <a href="javascript:void(0)" onclick="shareEmail()" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg">
                    <i class="fas fa-envelope text-blue-600 mr-2"></i>Email
                </a>
            </div>
        </div>
    </div>
</div>

<div id="printableArea">
    <!-- Print Header (Hidden on screen) -->
    <div class="print-only" style="display: none;">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold">{{ auth()->user()->business->name }}</h1>
            <h2 class="text-xl text-gray-600">Annual Performance Report</h2>
            <p class="text-gray-600">Year: {{ $selectedYear }}</p>
            <hr class="my-4">
        </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold mt-2">UGX {{ number_format($totalRevenue, 0) }}</p>
                    @if($revenueGrowth != 0)
                        <p class="text-green-100 text-xs mt-1">
                            <i class="fas fa-{{ $revenueGrowth > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                            {{ number_format(abs($revenueGrowth), 1) }}% vs {{ $previousYear }}
                        </p>
                    @endif
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Sales</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalSales) }}</p>
                    @if($salesGrowth != 0)
                        <p class="text-blue-100 text-xs mt-1">
                            <i class="fas fa-{{ $salesGrowth > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                            {{ number_format(abs($salesGrowth), 1) }}% vs {{ $previousYear }}
                        </p>
                    @endif
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Sale -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Average Sale</p>
                    <p class="text-3xl font-bold mt-2">UGX {{ number_format($avgSale, 0) }}</p>
                    <p class="text-purple-100 text-xs mt-1">Per transaction</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Unique Customers -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Active Customers</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($uniqueCustomers) }}</p>
                    <p class="text-orange-100 text-xs mt-1">Unique customers</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Best & Worst Months -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>Best Performing Month
            </h3>
            @if($bestMonth)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-green-600">
                            {{ \Carbon\Carbon::create()->month($bestMonth->month)->format('F') }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $bestMonth->sales }} sales</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-green-600">
                            UGX {{ number_format($bestMonth->revenue, 0) }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-gray-500">No data available</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-line text-red-500 mr-2"></i>Slowest Month
            </h3>
            @if($worstMonth)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-red-600">
                            {{ \Carbon\Carbon::create()->month($worstMonth->month)->format('F') }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $worstMonth->sales }} sales</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-red-600">
                            UGX {{ number_format($worstMonth->revenue, 0) }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-gray-500">No data available</p>
            @endif
        </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>Monthly Revenue Trend
        </h3>
        <canvas id="monthlyRevenueChart" height="80"></canvas>
    </div>

    <!-- Quarterly Breakdown -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-calendar-check text-blue-600 mr-2"></i>Quarterly Performance
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($quarters as $quarter => $data)
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border-2 border-blue-200">
                    <p class="text-sm font-semibold text-blue-600">{{ $quarter }}</p>
                    <p class="text-2xl font-bold text-blue-800 mt-2">{{ number_format($data['sales']) }}</p>
                    <p class="text-xs text-blue-600">sales</p>
                    <p class="text-sm font-semibold text-green-600 mt-2">UGX {{ number_format($data['revenue'], 0) }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Add this section after Quarterly Breakdown -->

<!-- Monthly Top Performers -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-medal text-yellow-500 mr-2"></i>Top Performer Per Month
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($monthlyTopPerformers as $month => $data)
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border-2 {{ $data['staff'] ? 'border-green-200' : 'border-gray-200' }}">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-700">{{ $data['month_name'] }}</p>
                    @if($data['staff'])
                        <i class="fas fa-trophy text-yellow-500"></i>
                    @endif
                </div>
                
                @if($data['staff'])
                    <div class="mt-2">
                        <p class="font-bold text-indigo-600 text-sm">{{ $data['staff']->name }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $data['sales_count'] }} sales</p>
                        <p class="text-sm font-semibold text-green-600 mt-1">
                            UGX {{ number_format($data['revenue'], 0) }}
                        </p>
                    </div>
                @else
                    <p class="text-xs text-gray-400 mt-2">No sales this month</p>
                @endif
            </div>
        @endforeach
    </div>
</div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Payment Methods Pie Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-credit-card text-purple-600 mr-2"></i>Payment Methods
            </h3>
            <canvas id="paymentMethodsChart" height="200"></canvas>
        </div>

        <!-- Sales Distribution -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-green-600 mr-2"></i>Quarterly Revenue Distribution
            </h3>
            <canvas id="quarterlyPieChart" height="200"></canvas>
        </div>
    </div>

    <!-- Top Performers Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Top Products -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-box text-blue-600 mr-2"></i>Top 10 Products
            </h3>
            <div class="space-y-3">
                @forelse($topProducts as $index => $product)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ Str::limit($product->name, 20) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($product->units_sold) }} units</p>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-green-600">UGX {{ number_format($product->revenue, 0) }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No products data</p>
                @endforelse
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-user-tie text-green-600 mr-2"></i>Top 10 Customers
            </h3>
            <div class="space-y-3">
                @forelse($topCustomers as $index => $customer)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ Str::limit($customer->name, 20) }}</p>
                                <p class="text-xs text-gray-500">{{ $customer->sales_count }} purchases</p>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-green-600">UGX {{ number_format($customer->sales_sum_total, 0) }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No customers data</p>
                @endforelse
            </div>
        </div>

        <!-- Top Staff -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-users text-purple-600 mr-2"></i>Top 10 Staff
            </h3>
            <div class="space-y-3">
                @forelse($topStaff as $index => $staff)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center space-x-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ Str::limit($staff->name, 20) }}</p>
                                <p class="text-xs text-gray-500">{{ $staff->sales_count }} sales</p>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-purple-600">UGX {{ number_format($staff->sales_sum_total, 0) }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No staff data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Print Footer (Hidden on screen) -->
    <div class="print-only" style="display: none;">
        <hr class="my-4">
        <div class="text-sm text-gray-600">
            <p>Generated on: {{ now()->format('d M Y h:i A') }}</p>
            <p>Generated by: {{ auth()->user()->name }}</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-only {
            display: block !important;
        }
        
        .shadow-lg, .rounded-xl {
            box-shadow: none !important;
        }
        
        body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // ========================================
    // MONTHLY REVENUE BAR CHART (FIXED)
    // ========================================
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart');
    
    if (monthlyRevenueCtx) {
        new Chart(monthlyRevenueCtx, {
            type: 'bar',
            data: {
                labels: @json($monthNames),
                datasets: [{
                    label: 'Revenue (UGX)',
                    data: @json($monthlyRevenue),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Sales Count',
                    data: @json($monthlySales),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true, 
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'UGX ' + value.toLocaleString();
                            }
                        },
                        title: {
                            display: true,
                            text: 'Revenue (UGX)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Sales Count'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += 'UGX ' + context.parsed.y.toLocaleString();
                                } else {
                                    label += context.parsed.y.toLocaleString() + ' sales';
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }

    // ========================================
    // PAYMENT METHODS PIE CHART (FIXED)
    // ========================================
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart');
    
    if (paymentMethodsCtx) {
        const paymentLabels = @json($paymentMethods->pluck('payment_method')->map(fn($m) => ucfirst(str_replace('_', ' ', $m))));
        const paymentData = @json($paymentMethods->pluck('total'));
        
        if (paymentData.length > 0 && paymentData.some(val => val > 0)) {
            new Chart(paymentMethodsCtx, {
                type: 'doughnut',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        data: paymentData,
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(14, 165, 233, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
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
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
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
        } else {
            paymentMethodsCtx.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">No payment data available</p>';
        }
    }

    // ========================================
    // QUARTERLY PIE CHART (FIXED)
    // ========================================
    const quarterlyPieCtx = document.getElementById('quarterlyPieChart');
    
    if (quarterlyPieCtx) {
        const quarterlyData = @json(array_column($quarters, 'revenue'));
        
        if (quarterlyData.some(val => val > 0)) {
            new Chart(quarterlyPieCtx, {
                type: 'pie',
                data: {
                    labels: ['Q1 (Jan-Mar)', 'Q2 (Apr-Jun)', 'Q3 (Jul-Sep)', 'Q4 (Oct-Dec)'],
                    datasets: [{
                        data: quarterlyData,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(168, 85, 247, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
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
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
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
        } else {
            quarterlyPieCtx.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">No quarterly data available</p>';
        }
    }

    // ========================================
    // SHARE & DROPDOWN FUNCTIONS
    // ========================================
    function toggleShareDropdown() {
        const dropdown = document.getElementById('shareDropdown');
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('shareDropdown');
        const shareButton = event.target.closest('.share-dropdown');
        
        if (!shareButton && dropdown) {
            dropdown.classList.add('hidden');
        }
    });

    function printReport() {
        window.print();
    }

    function shareWhatsApp() {
        const businessName = "{{ auth()->user()->business->name }}";
        const year = "{{ $selectedYear }}";
        const totalRevenue = "{{ number_format($totalRevenue, 0) }}";
        const totalSales = "{{ $totalSales }}";
        const growth = "{{ number_format($revenueGrowth, 1) }}";
        
        const message = `📊 *Annual Performance Report ${year}* 📊\n\n` +
                       `🏢 ${businessName}\n\n` +
                       `💰 Total Revenue: UGX ${totalRevenue}\n` +
                       `🛒 Total Sales: ${totalSales}\n` +
                       `📈 Growth: ${growth}% vs last year\n\n` +
                       `Generated by POS System`;
        
        const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
        
        document.getElementById('shareDropdown').classList.add('hidden');
    }

    function shareEmail() {
        const businessName = "{{ auth()->user()->business->name }}";
        const year = "{{ $selectedYear }}";
        const totalRevenue = "{{ number_format($totalRevenue, 0) }}";
        const totalSales = "{{ $totalSales }}";
        const growth = "{{ number_format($revenueGrowth, 1) }}";
        
        const subject = `Annual Performance Report - ${year}`;
        const body = `Annual Performance Report ${year}\n\n` +
                    `Business: ${businessName}\n\n` +
                    `Total Revenue: UGX ${totalRevenue}\n` +
                    `Total Sales: ${totalSales}\n` +
                    `Growth: ${growth}% vs last year\n\n` +
                    `Generated by POS System`;
        
        const mailtoUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.location.href = mailtoUrl;
        
        document.getElementById('shareDropdown').classList.add('hidden');
    }
</script>
@endpush