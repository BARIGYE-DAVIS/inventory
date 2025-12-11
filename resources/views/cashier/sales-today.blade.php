@extends('layouts.cashier-layout')

@section('title', 'Today\'s Sales')

@section('page-title')
    <i class="fas fa-calendar-day text-green-600 mr-2"></i>Today's Sales
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Sales Today</p>
                    <p class="text-4xl font-bold mt-2">{{ $sales->count() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-shopping-cart text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold mt-2">UGX {{ number_format($totalAmount, 0) }}</p>
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
    </div>

    <!-- Hourly Performance Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-bar text-green-600 mr-2"></i>Hourly Performance
        </h3>
        <canvas id="hourlyChart" height="80"></canvas>
    </div>

    <!-- Today's Sales List -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-list text-green-600 mr-2"></i>All Sales Today
            </h3>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>New Sale
            </a>
        </div>

        @if($sales->count() > 0)
        <div class="space-y-3">
            @foreach($sales as $sale)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $sale->sale_number }}</p>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>{{ $sale->sale_date->format('h:i A') }} • 
                        <i class="fas fa-user mr-1"></i>{{ $sale->customer->name ?? 'Walk-in' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold text-green-600">UGX {{ number_format($sale->total, 0) }}</p>
                    <div class="space-x-2 mt-2">
                        <a href="{{ route('sales.show', $sale->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="text-sm text-green-600 hover:text-green-800">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No sales made today yet</p>
            <a href="{{ route('pos.index') }}" class="inline-block mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus-circle mr-2"></i>Make Your First Sale Today
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: ['8AM', '9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM'],
            datasets: [{
                label: 'Sales (UGX)',
                data: [
                    @foreach(range(8, 18) as $hour)
                    {{ \App\Models\Sale::where('user_id', auth()->id())->whereDate('sale_date', today())->whereRaw('HOUR(sale_date) = ?', [$hour])->sum('total') }},
                    @endforeach
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
</script>
@endpush