@extends('layouts.cashier-layout')

@section('title', 'Search Products')

@section('page-title')
    <i class="fas fa-search text-green-600 mr-2"></i>Search Products
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" 
                           id="liveSearchInput"
                           placeholder="Type to search by name, SKU, or barcode..." 
                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                    <div id="searchSpinner" class="hidden absolute right-3 top-4">
                        <i class="fas fa-spinner fa-spin text-green-600"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-keyboard mr-1"></i>Press Ctrl+K to focus | Esc to clear
                </p>
            </div>

            <!-- Category Filter -->
            <div>
                <select id="liveCategoryFilter"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Results Count -->
        <div class="mt-4 text-sm font-semibold text-gray-700">
            <i class="fas fa-box text-green-600 mr-1"></i>
            <span id="resultsCount">{{ $products->total() }} products found</span>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-th-large text-green-600 mr-2"></i>Products
        </h3>

        <div id="productsGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($products as $product)
            <a href="{{ route('products.show', $product->id) }}" class="border border-gray-200 rounded-lg p-3 hover:shadow-lg transition cursor-pointer">
                
                <!-- Product Image -->
                <div class="aspect-square bg-gray-100 rounded-lg mb-2 overflow-hidden">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-box text-4xl text-gray-300"></i>
                    </div>
                    @endif
                </div>

                <!-- Product Info -->
                <h4 class="font-semibold text-sm text-gray-900 truncate" title="{{ $product->name }}">
                    {{ $product->name }}
                </h4>
                @if($product->sku)
                <p class="text-xs text-gray-500">{{ $product->sku }}</p>
                @endif
                <p class="text-lg font-bold text-green-600 mt-1">
                    UGX {{ number_format($product->selling_price, 0) }}
                </p>
                <p class="text-xs {{ $product->quantity < 10 ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                    Stock: {{ $product->quantity }} {{ $product->unit ?? 'pcs' }}
                </p>
                
                @if($product->category)
                <span class="inline-block mt-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                    {{ $product->category->name }}
                </span>
                @endif
            </a>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-search-minus text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No products found</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Live Search Functionality
let searchTimeout = null;
const searchInput = document.getElementById('liveSearchInput');
const categoryFilter = document.getElementById('liveCategoryFilter');
const productsGrid = document.getElementById('productsGrid');
const resultsCount = document.getElementById('resultsCount');
const searchSpinner = document.getElementById('searchSpinner');

// Function to fetch products
async function fetchProducts() {
    const searchTerm = searchInput.value.trim();
    const categoryId = categoryFilter.value;

    // Show loading
    searchSpinner.classList.remove('hidden');
    productsGrid.style.opacity = '0.5';

    try {
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (categoryId) params.append('category', categoryId);
        params.append('ajax', '1');

        const response = await fetch('{{ route("products.index") }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            productsGrid.innerHTML = data.html;
            resultsCount.textContent = data.count + ' products found';
        }
    } catch (error) {
        console.error('Error:', error);
        productsGrid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500"><i class="fas fa-exclamation-triangle text-5xl mb-3"></i><p>Error loading products</p></div>';
    } finally {
        searchSpinner.classList.add('hidden');
        productsGrid.style.opacity = '1';
    }
}

// Search input - wait 300ms after user stops typing
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(fetchProducts, 300);
});

// Category filter - instant update
categoryFilter.addEventListener('change', fetchProducts);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.focus();
    }
    if (e.key === 'Escape') {
        searchInput.value = '';
        categoryFilter.value = '';
        fetchProducts();
    }
});

// Auto-focus on load
window.addEventListener('load', () => searchInput.focus());
</script>
@endpush

@push('styles')
<style>
#productsGrid {
    transition: opacity 0.3s ease;
}
</style>
@endpush