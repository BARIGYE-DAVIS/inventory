@extends('layouts.app')

@section('title', 'Stock Reconciliation Report')
@section('page-title', 'Stock Reconciliation Analysis')

@section('content')
<div class="space-y-6">

    <!-- Period Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Stock Reconciliation Report</h2>
                <p class="text-indigo-200">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ $period->period_start->format('M d, Y') }} to {{ $period->period_end->format('M d, Y') }}
                </p>
                <p class="text-indigo-200 mt-1">
                    <i class="fas fa-box mr-2"></i>
                    <strong>{{ $product->name }}</strong>
                </p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold">
                    {{ number_format($reconciliation['final_accepted_stock'], 2) }}
                </div>
                <p class="text-indigo-200 text-sm mt-1">Final Accepted Stock</p>
            </div>
        </div>
    </div>

    <!-- Main Reconciliation Breakdown -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-balance-scale text-indigo-600 mr-2"></i>Reconciliation Breakdown
        </h3>

        <div class="space-y-6">

            <!-- Opening Stock to System Calculated -->
            <div class="border-l-4 border-blue-500 bg-blue-50 p-6 rounded-r-lg">
                <h4 class="text-lg font-bold text-gray-800 mb-4">System Stock Calculation</h4>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-blue-200">
                        <span class="text-gray-700 font-semibold">Opening Stock (from products.opening_stock)</span>
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($reconciliation['opening_stock'], 2) }} units</span>
                    </div>

                    <div class="flex items-center justify-center text-gray-600 font-bold">
                        <i class="fas fa-plus text-green-600"></i>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white rounded border border-green-200">
                        <span class="text-gray-700 font-semibold">Purchases (sum of all purchase_items)</span>
                        <span class="text-2xl font-bold text-green-600">+{{ number_format($reconciliation['purchases'], 2) }} units</span>
                    </div>

                    <div class="flex items-center justify-center text-gray-600 font-bold">
                        <i class="fas fa-minus text-red-600"></i>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white rounded border border-red-200">
                        <span class="text-gray-700 font-semibold">Sales (sum of all sale_items)</span>
                        <span class="text-2xl font-bold text-red-600">-{{ number_format($reconciliation['sales'], 2) }} units</span>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-3 mt-3">
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-lg border-2 border-indigo-500">
                            <span class="text-gray-800 font-bold text-lg">System Calculated Stock</span>
                            <span class="text-3xl font-bold text-indigo-600">{{ number_format($reconciliation['system_calculated_stock'], 2) }} units</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physical Count & Variance -->
            @if($reconciliation['is_reconciled'])
            <div class="border-l-4 {{ $reconciliation['is_loss'] ? 'border-red-500' : ($reconciliation['is_gain'] ? 'border-green-500' : 'border-gray-500') }} bg-gray-50 p-6 rounded-r-lg">
                <h4 class="text-lg font-bold text-gray-800 mb-4">Physical Count & Variance Analysis</h4>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded border {{ $reconciliation['is_loss'] ? 'border-red-200' : ($reconciliation['is_gain'] ? 'border-green-200' : 'border-gray-200') }}">
                        <span class="text-gray-700 font-semibold">Physical Count (from stock_adjustments)</span>
                        <span class="text-2xl font-bold {{ $reconciliation['is_loss'] ? 'text-red-600' : ($reconciliation['is_gain'] ? 'text-green-600' : 'text-gray-600') }}">{{ number_format($reconciliation['physical_count'], 2) }} units</span>
                    </div>

                    <div class="flex items-center justify-center text-gray-600 font-bold">
                        <i class="fas fa-minus text-orange-600"></i>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-white rounded border border-indigo-200">
                        <span class="text-gray-700 font-semibold">System Calculated Stock</span>
                        <span class="text-2xl font-bold text-indigo-600">{{ number_format($reconciliation['system_calculated_stock'], 2) }} units</span>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-3 mt-3">
                        <div class="flex items-center justify-between p-4 rounded-lg border-2 {{ $reconciliation['is_loss'] ? 'bg-red-100 border-red-500' : ($reconciliation['is_gain'] ? 'bg-green-100 border-green-500' : 'bg-gray-100 border-gray-500') }}">
                            <div>
                                <span class="text-gray-800 font-bold text-lg">Variance ({{ $reconciliation['is_loss'] ? 'Loss' : ($reconciliation['is_gain'] ? 'Gain' : 'Match') }})</span>
                                <p class="text-sm text-gray-600 mt-1">{{ number_format(abs($reconciliation['variance_percentage']), 2) }}% {{ $reconciliation['is_loss'] ? 'loss' : ($reconciliation['is_gain'] ? 'gain' : 'match') }}</p>
                            </div>
                            <span class="text-3xl font-bold {{ $reconciliation['is_loss'] ? 'text-red-600' : ($reconciliation['is_gain'] ? 'text-green-600' : 'text-gray-600') }}">
                                {{ $reconciliation['variance'] > 0 ? '+' : '' }}{{ number_format($reconciliation['variance'], 2) }} units
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Adjustment Details -->
                @if($reconciliation['latest_adjustment'])
                <div class="mt-6 pt-6 border-t border-gray-300">
                    <h5 class="font-bold text-gray-700 mb-3">Adjustment Details</h5>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white p-3 rounded border border-gray-200">
                            <p class="text-gray-600">Reason</p>
                            <p class="font-semibold text-gray-800">{{ $reconciliation['adjustment_reason'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white p-3 rounded border border-gray-200">
                            <p class="text-gray-600">Adjustment Date</p>
                            <p class="font-semibold text-gray-800">{{ $reconciliation['adjustment_date']?->format('M d, Y H:i') ?? 'N/A' }}</p>
                        </div>
                        @if($reconciliation['adjustment_notes'])
                        <div class="col-span-2 bg-white p-3 rounded border border-gray-200">
                            <p class="text-gray-600">Notes</p>
                            <p class="font-semibold text-gray-800">{{ $reconciliation['adjustment_notes'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Final Reconciliation -->
            <div class="border-l-4 border-purple-500 bg-purple-50 p-6 rounded-r-lg">
                <h4 class="text-lg font-bold text-gray-800 mb-4">Final Reconciliation</h4>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-white rounded border border-purple-200">
                        <span class="text-gray-700 font-semibold">Reconciliation Adjustment</span>
                        <span class="text-2xl font-bold {{ $reconciliation['reconciliation_adjustment'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $reconciliation['reconciliation_adjustment'] >= 0 ? '+' : '' }}{{ number_format($reconciliation['reconciliation_adjustment'], 2) }} units
                        </span>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-3 mt-3">
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-100 to-indigo-100 rounded-lg border-2 border-purple-500">
                            <span class="text-gray-800 font-bold text-lg">Final Accepted Stock</span>
                            <span class="text-3xl font-bold text-purple-600">{{ number_format($reconciliation['final_accepted_stock'], 2) }} units</span>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <div class="border-l-4 border-yellow-500 bg-yellow-50 p-6 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-yellow-600 text-2xl mr-4"></i>
                    <div>
                        <h4 class="font-bold text-gray-800">Not Yet Reconciled</h4>
                        <p class="text-gray-600 text-sm mt-1">
                            Physical count has not been recorded for this period. The system calculated stock 
                            (<strong>{{ number_format($reconciliation['system_calculated_stock'], 2) }} units</strong>) 
                            is being used as the final accepted stock.
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Reconciliation Status</p>
                    <p class="text-xl font-bold mt-2">
                        @if($reconciliation['is_reconciled'])
                            <span class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Reconciled</span>
                        @else
                            <span class="text-yellow-600"><i class="fas fa-clock mr-2"></i>Pending</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Variance Status</p>
                    <p class="text-xl font-bold mt-2">
                        @if($reconciliation['has_variance'])
                            @if($reconciliation['is_loss'])
                                <span class="text-red-600"><i class="fas fa-arrow-down mr-2"></i>Loss</span>
                            @else
                                <span class="text-green-600"><i class="fas fa-arrow-up mr-2"></i>Gain</span>
                            @endif
                        @else
                            <span class="text-gray-600"><i class="fas fa-equals mr-2"></i>Match</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Variance Amount</p>
                    <p class="text-2xl font-bold {{ $reconciliation['is_loss'] ? 'text-red-600' : 'text-green-600' }} mt-2">
                        {{ $reconciliation['variance'] >= 0 ? '+' : '' }}{{ number_format($reconciliation['variance'], 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format(abs($reconciliation['variance_percentage']), 2) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h4 class="font-bold text-blue-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>How This Reconciliation Works
        </h4>
        <div class="space-y-2 text-sm text-blue-800">
            <p><strong>Opening Stock:</strong> The starting quantity from products.opening_stock for this period</p>
            <p><strong>Purchases:</strong> Sum of all purchase_items received during the period</p>
            <p><strong>Sales:</strong> Sum of all sale_items sold during the period</p>
            <p><strong>System Calculated Stock:</strong> Opening + Purchases - Sales = what the system expects</p>
            <p><strong>Physical Count:</strong> Actual count taken during stock adjustment/stock taking session</p>
            <p><strong>Variance:</strong> The difference between physical count and system calculated stock</p>
            <p><strong>Final Accepted Stock:</strong> Uses physical count if available, otherwise uses system calculated stock</p>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex gap-4">
        <a href="{{ route('inventory.periods') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Periods
        </a>
        <a href="{{ route('inventory.show', $product->id) }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-box mr-2"></i>View Product Details
        </a>
    </div>

</div>
@endsection
