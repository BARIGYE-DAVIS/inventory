@extends('layouts.app')

@section('title', 'Staff Details - ' . $staff->name)

@section('page-title')
    <i class="fas fa-user-circle text-indigo-600 mr-2"></i>Staff Details - {{ $staff->name }}
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Staff Profile Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-3xl">{{ substr($staff->name, 0, 1) }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $staff->name }}</h2>
                    <p class="text-gray-600 mt-1">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            {{ $staff->role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 
                               ($staff->role->name === 'manager' ? 'bg-blue-100 text-blue-800' : 
                               ($staff->role->name === 'cashier' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ $staff->role->display_name }}
                        </span>
                        <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full
                            {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $staff->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                    <div class="mt-2 space-y-1">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-envelope mr-2"></i>{{ $staff->email }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-phone mr-2"></i>{{ $staff->phone }}
                        </p>
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-calendar mr-2"></i>Joined {{ $staff->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('staff.edit', $staff) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Performance Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Today -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase">Today</h3>
                <i class="fas fa-calendar-day text-blue-500 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $todaySales }}</p>
            <p class="text-sm text-gray-600 mt-1">Sales</p>
            <p class="text-lg font-semibold text-green-600 mt-2">UGX {{ number_format($todayRevenue, 0) }}</p>
        </div>

        <!-- This Week -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase">This Week</h3>
                <i class="fas fa-calendar-week text-green-500 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $weekSales }}</p>
            <p class="text-sm text-gray-600 mt-1">Sales</p>
            <p class="text-lg font-semibold text-green-600 mt-2">UGX {{ number_format($weekRevenue, 0) }}</p>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase">This Month</h3>
                <i class="fas fa-calendar-alt text-purple-500 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $monthSales }}</p>
            <p class="text-sm text-gray-600 mt-1">Sales</p>
            <p class="text-lg font-semibold text-green-600 mt-2">UGX {{ number_format($monthRevenue, 0) }}</p>
        </div>

        <!-- All Time -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase">All Time</h3>
                <i class="fas fa-infinity text-indigo-500 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalSales }}</p>
            <p class="text-sm text-gray-600 mt-1">Sales</p>
            <p class="text-lg font-semibold text-green-600 mt-2">UGX {{ number_format($totalRevenue, 0) }}</p>
        </div>
    </div>

    <!-- Sales Trend Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
            Sales Trend (Last 7 Days)
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sales Count</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($salesTrend as $trend)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($trend->date)->format('D, M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold">{{ $trend->count }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                            UGX {{ number_format($trend->revenue, 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            No sales data available for the last 7 days
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Sales -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-receipt text-indigo-600 mr-2"></i>
            Recent Sales (Last 10)
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentSales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-indigo-600">
                            {{ $sale->sale_number }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $sale->sale_date->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $sale->customer->name ?? 'Walk-in' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            {{ $sale->items->count() }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                            UGX {{ number_format($sale->total, 0) }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-eye mr-1"></i>View Receipt
                            </a>
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

</div>
@endsection