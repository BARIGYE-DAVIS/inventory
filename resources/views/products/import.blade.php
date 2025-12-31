@extends('layouts.app')

@section('title', 'Import Products')

@section('page-title')
    <i class="fas fa-file-import text-green-600 mr-2"></i>Import Products
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Import Products</h2>
                <p class="text-gray-600 mt-1">Quickly add multiple products using a CSV or Excel file</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-times text-2xl"></i>
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Supported Formats -->
        <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-600">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-file-alt text-blue-600 text-2xl mt-1"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900">Supported Formats</h3>
                    <p class="text-blue-700 text-sm mt-2">
                        <i class="fas fa-check-circle mr-2"></i>CSV (.csv)<br>
                        <i class="fas fa-check-circle mr-2"></i>Excel (.xlsx, .xls)<br>
                        <i class="fas fa-check-circle mr-2"></i>Maximum file size: 5MB
                    </p>
                </div>
            </div>
        </div>

        <!-- Required Columns -->
        <div class="bg-purple-50 rounded-lg p-6 border-l-4 border-purple-600">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-list text-purple-600 text-2xl mt-1"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-purple-900">Required Columns</h3>
                    <p class="text-purple-700 text-sm mt-2">
                        <span class="font-semibold">Name</span> - Product name<br>
                        <span class="font-semibold">SKU</span> - Unique product code
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional Fields Info -->
    <div class="bg-green-50 rounded-lg p-6 mb-6 border-l-4 border-green-600">
        <h3 class="text-lg font-semibold text-green-900 mb-3">Optional Columns</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-green-800">
            <div><i class="fas fa-tag mr-2"></i>Category - Product category</div>
            <div><i class="fas fa-align-left mr-2"></i>Description - Product details</div>
            <div><i class="fas fa-dollar-sign mr-2"></i>Cost Price - Cost per unit</div>
            <div><i class="fas fa-tag mr-2"></i>Selling Price - Sale price per unit</div>
            <div><i class="fas fa-boxes mr-2"></i>Quantity - Initial stock quantity</div>
            <div><i class="fas fa-cube mr-2"></i>Unit - Unit of measurement (pcs, kg, etc.)</div>
            <div><i class="fas fa-barcode mr-2"></i>Barcode - Product barcode</div>
            <div><i class="fas fa-calendar mr-2"></i>Expiry Date - Expiration date (YYYY-MM-DD)</div>
        </div>
    </div>

    <!-- Import Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- File Upload -->
            <div class="mb-6">
                <label for="file" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-cloud-upload-alt mr-2 text-indigo-600"></i>Select File to Import
                </label>
                <div class="relative">
                    <input 
                        type="file" 
                        id="file" 
                        name="file" 
                        accept=".csv,.txt,.xlsx,.xls" 
                        required
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition-colors @error('file') border-red-500 @enderror"
                        onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'No file selected'"
                    >
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-2"></i>
                    Selected file: <span id="file-name" class="font-semibold text-gray-900">No file selected</span>
                </div>
                @error('file')
                    <div class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Template Download -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-700 mb-3">
                    <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
                    Not sure about the format? Download our template to get started
                </p>
                <a href="{{ route('products.import.template') }}" class="inline-block px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                    <i class="fas fa-download mr-2"></i>Download Template
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold flex items-center justify-center"
                >
                    <i class="fas fa-file-import mr-2"></i>Import Products
                </button>
                <a 
                    href="{{ route('products.index') }}" 
                    class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-semibold flex items-center justify-center"
                >
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Example CSV Content -->
    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-file-csv mr-2 text-green-600"></i>Example File Format
        </h3>
        <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto">
            <pre class="text-xs text-gray-700 font-mono"><code>Name,SKU,Category,Description,Cost Price,Selling Price,Quantity,Unit,Barcode,Expiry Date
Laptop HP,SKU001,Electronics,HP Gaming Laptop,25000.00,35000.00,10,pcs,1234567890,2025-12-31
USB Cable,SKU002,Accessories,Type-C USB Cable,50.00,100.00,100,pcs,0987654321,2026-06-30
Wireless Mouse,SKU003,Electronics,Logitech Wireless,300.00,500.00,25,pcs,5555555555,2025-12-31</code></pre>
        </div>
    </div>
</div>
@endsection
