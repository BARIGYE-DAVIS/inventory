@extends('layouts.app')

@section('title', 'All Products')

@section('page-title')
    <i class="fas fa-box text-indigo-600 mr-2"></i>All Products
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-lg p-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Products List</h2>
            <p class="text-gray-600 text-sm mt-1">Manage your product inventory</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('products.import.show') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                <i class="fas fa-file-import mr-2"></i>
                Import Products
            </a>
            <a href="{{ route('products.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Product
            </a>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <form method="GET" action="{{ route('products.index') }}" id="filterForm" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- ✅ LIVE SEARCH (No button needed) -->
            <div class="relative">
                <input type="text" 
                       name="search" 
                       id="searchInput" 
                       value="{{ request('search') }}"
                       placeholder="Search by name, SKU, or barcode..." 
                       class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                
                <!-- ✅ Loading Spinner (shows when searching) -->
                <div id="searchSpinner" class="hidden absolute right-3 top-3">
                    <i class="fas fa-spinner fa-spin text-indigo-600"></i>
                </div>
            </div>

            <!-- Category Filter (Auto-submit) -->
            <div>
                <select name="category_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter (Auto-submit) -->
            <div>
                <select name="status" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            <!-- Reset Button Only -->
            <div class="flex space-x-2">
                <a href="{{ route('products.index') }}" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center justify-center">
                    <i class="fas fa-redo mr-1"></i> Reset All
                </a>
            </div>
        </div>
    </form>

    <!-- Active Filters Display -->
    @if(request()->hasAny(['search', 'category_id', 'status']))
    <div class="mb-4 flex flex-wrap gap-2">
        <span class="text-sm text-gray-600 font-semibold">Active Filters:</span>
        
        @if(request('search'))
        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm flex items-center">
            <i class="fas fa-search mr-1"></i>
            Search: "{{ request('search') }}"
            <a href="{{ route('products.index', array_filter(request()->except('search'))) }}" class="ml-2 text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-times"></i>
            </a>
        </span>
        @endif

        @if(request('category_id'))
        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm flex items-center">
            <i class="fas fa-folder mr-1"></i>
            Category: {{ $categories->where('id', request('category_id'))->first()->name ?? 'Unknown' }}
            <a href="{{ route('products.index', array_filter(request()->except('category_id'))) }}" class="ml-2 text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </a>
        </span>
        @endif

        @if(request('status'))
        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm flex items-center">
            <i class="fas fa-info-circle mr-1"></i>
            Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
            <a href="{{ route('products.index', array_filter(request()->except('status'))) }}" class="ml-2 text-yellow-600 hover:text-yellow-800">
                <i class="fas fa-times"></i>
            </a>
        </span>
        @endif
    </div>
    @endif

    <!-- Products Count -->
    <div class="mb-4">
        <p class="text-sm text-gray-600">
            Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() ?? 0 }}</span> 
            to <span class="font-semibold text-gray-900">{{ $products->lastItem() ?? 0 }}</span> 
            of <span class="font-semibold text-gray-900">{{ $products->total() }}</span> products
        </p>
    </div>

    <!-- Products Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <!-- Product Name -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $product->unit }}</p>
                        </div>
                    </td>

                    <!-- SKU -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-700 font-mono">{{ $product->sku }}</span>
                    </td>

                    <!-- Category -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-600">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>

                    <!-- Quantity with Color Coding -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            @if($product->quantity <= 0)
                                bg-red-100 text-red-800
                            @elseif($product->quantity <= $product->reorder_level)
                                bg-yellow-100 text-yellow-800
                            @else
                                bg-green-100 text-green-800
                            @endif">
                            {{ number_format($product->quantity, 0) }} {{ $product->unit }}
                        </span>
                    </td>

                    <!-- Selling Price -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">
                            UGX {{ number_format($product->selling_price, 0) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            Cost: UGX {{ number_format($product->cost_price, 0) }}
                        </div>
                    </td>

                    <!-- Expiry Status -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($product->track_expiry && $product->expiry_date)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($product->getExpiryStatusColor() === 'red')
                                    bg-red-100 text-red-800
                                @elseif($product->getExpiryStatusColor() === 'yellow')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-green-100 text-green-800
                                @endif">
                                {{ $product->getExpiryStatusText() }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $product->expiry_date->format('M d, Y') }}
                            </div>
                        @else
                            <span class="text-xs text-gray-400">No tracking</span>
                        @endif
                    </td>

                    <!-- Active Status -->
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-3">
                            <a href="{{ route('products.edit', $product) }}" 
                               class="text-indigo-600 hover:text-indigo-900" 
                               title="Edit Product">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" 
                                  action="{{ route('products.destroy', $product) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900" 
                                        title="Delete Product">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg font-medium mb-2">No products found</p>
                            <p class="text-gray-400 text-sm mb-4">
                                @if(request()->hasAny(['search', 'category_id', 'status']))
                                    Try adjusting your filters or 
                                    <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">clear all filters</a>
                                @else
                                    Get started by adding your first product
                                @endif
                            </p>
                            <a href="{{ route('products.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Add Your First Product
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>

@push('scripts')
<script>
    // ✅ LIVE SEARCH - Auto-submit after user stops typing
    let searchTimer;
    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const filterForm = document.getElementById('filterForm');

    searchInput.addEventListener('input', function() {
        // Clear previous timer
        clearTimeout(searchTimer);
        
        // Show loading spinner
        searchSpinner.classList.remove('hidden');
        
        // Wait 500ms after user stops typing, then submit
        searchTimer = setTimeout(function() {
            filterForm.submit();
        }, 500); // ✅ Adjust delay here (500ms = 0.5 seconds)
    });

    // ✅ Optional: Submit on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimer);
            filterForm.submit();
        }
    });
</script>
@endpush
@endsection