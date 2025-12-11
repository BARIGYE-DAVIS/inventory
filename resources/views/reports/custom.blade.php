@extends('layouts.app')

@section('title', 'Custom Report Generator')

@section('page-title')
    <i class="fas fa-cogs text-indigo-600 mr-2"></i>Custom Report Generator
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6">
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Build Your Custom Report</h2>
            <p class="text-gray-600 mt-1">Select the criteria below to generate a customized report</p>
        </div>

        <form method="POST" action="{{ route('reports.generate') }}">
            @csrf

            <!-- Report Type -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>Report Type
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="report_type" value="sales" checked class="mr-3">
                        <div>
                            <p class="font-semibold text-gray-900">Sales Report</p>
                            <p class="text-xs text-gray-500">Transactions & revenue</p>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="report_type" value="products" class="mr-3">
                        <div>
                            <p class="font-semibold text-gray-900">Product Report</p>
                            <p class="text-xs text-gray-500">Product performance</p>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="report_type" value="inventory" class="mr-3">
                        <div>
                            <p class="font-semibold text-gray-900">Inventory Report</p>
                            <p class="text-xs text-gray-500">Stock levels</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Date Range -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar text-indigo-600 mr-1"></i>Start Date
                    </label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar text-indigo-600 mr-1"></i>End Date
                    </label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ now()->format('Y-m-d') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-filter text-indigo-600 mr-2"></i>Filters (Optional)
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Customer</label>
                        <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Export Format -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-file-export text-indigo-600 mr-2"></i>Export Format
                </label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="export_format" value="view" checked class="mr-2">
                        <span class="text-sm text-gray-700">View in Browser</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="export_format" value="pdf" class="mr-2">
                        <span class="text-sm text-gray-700">Download PDF</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="export_format" value="excel" class="mr-2">
                        <span class="text-sm text-gray-700">Download Excel</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('dashboard') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-chart-line mr-2"></i>Generate Report
                </button>
            </div>
        </form>

    </div>
</div>
@endsection