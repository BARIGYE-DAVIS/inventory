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

    <!-- Products Table -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-list text-green-600 mr-2"></i>Products
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Product Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Category</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Stock</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $product->sku ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($product->category)
                                <span class="inline-block px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-green-600 text-right">
                            UGX {{ number_format($product->selling_price, 0) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right {{ $product->quantity < 10 ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                            {{ $product->quantity }} {{ $product->unit ?? 'pcs' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('products.show', $product->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-search-minus text-3xl mb-2"></i>
                            <p class="text-lg">No products found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
const productsTable = document.querySelector('tbody');
const resultsCount = document.getElementById('resultsCount');
const searchSpinner = document.getElementById('searchSpinner');

// Function to fetch products
async function fetchProducts() {
    const searchTerm = searchInput.value.trim();
    const categoryId = categoryFilter.value;

    // Show loading
    searchSpinner.classList.remove('hidden');
    productsTable.style.opacity = '0.5';

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
            productsTable.innerHTML = data.html;
            resultsCount.textContent = data.count + ' products found';
        }
    } catch (error) {
        console.error('Error:', error);
        productsTable.innerHTML = '<tr><td colspan="6" class="text-center py-12 text-red-500"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p>Error loading products</p></td></tr>';
    } finally {
        searchSpinner.classList.add('hidden');
        productsTable.style.opacity = '1';
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