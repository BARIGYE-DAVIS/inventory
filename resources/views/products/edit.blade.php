@extends('layouts.app')

@section('title', 'Edit Product')

@section('page-title')
    <i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Product
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6">
        
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-indigo-600 mr-1"></i>
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- SKU -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-barcode text-indigo-600 mr-1"></i>
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Barcode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-qrcode text-indigo-600 mr-1"></i>
                        Barcode (Optional)
                    </label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder text-indigo-600 mr-1"></i>
                        Category
                    </label>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-balance-scale text-indigo-600 mr-1"></i>
                        Unit <span class="text-red-500">*</span>
                    </label>
                    <select name="unit" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                        <option value="grams" {{ old('unit', $product->unit) == 'grams' ? 'selected' : '' }}>Grams (g)</option>
                        <option value="liters" {{ old('unit', $product->unit) == 'liters' ? 'selected' : '' }}>Liters (L)</option>
                        <option value="ml" {{ old('unit', $product->unit) == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                        <option value="boxes" {{ old('unit', $product->unit) == 'boxes' ? 'selected' : '' }}>Boxes</option>
                        <option value="cartons" {{ old('unit', $product->unit) == 'cartons' ? 'selected' : '' }}>Cartons</option>
                        <option value="dozen" {{ old('unit', $product->unit) == 'dozen' ? 'selected' : '' }}>Dozen</option>
                        <option value="pairs" {{ old('unit', $product->unit) == 'pairs' ? 'selected' : '' }}>Pairs</option>
                        <option value="meters" {{ old('unit', $product->unit) == 'meters' ? 'selected' : '' }}>Meters (m)</option>
                    </select>
                </div>

                <!-- ✅ QUANTITY (Editable) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-boxes text-green-600 mr-1"></i>
                        Quantity
                    </label>
                    <input type="number" name="quantity" value="{{ old('quantity', $product->quantity ?? 0) }}" min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Current stock quantity</p>
                </div>

                <!-- Cost Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill text-indigo-600 mr-1"></i>
                        Cost Price (UGX) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Selling Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-green-600 mr-1"></i>
                        Selling Price (UGX) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Reorder Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-1"></i>
                        Reorder Level
                    </label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Active Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-indigo-600 mr-1"></i>
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="is_active" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Current Image -->
                @if($product->image)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
                </div>
                @endif

                <!-- Product Image -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-image text-indigo-600 mr-1"></i>
                        Update Product Image
                    </label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current image</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-align-left text-indigo-600 mr-1"></i>
                        Description
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Expiry Tracking Section -->
                <div class="md:col-span-2 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex items-start">
                        <input type="checkbox" id="track_expiry" name="track_expiry" value="1" 
                               {{ old('track_expiry', $product->track_expiry) ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-indigo-600" onchange="toggleExpiryFields(this)">
                        <div class="ml-3">
                            <label for="track_expiry" class="font-medium text-blue-800 cursor-pointer">
                                <i class="fas fa-calendar-times mr-1"></i>
                                Track Expiry Date for this Product
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Expiry Fields -->
                <div id="expiryFields" class="md:col-span-2 {{ old('track_expiry', $product->track_expiry) ? '' : 'hidden' }} space-y-4 pl-4 border-l-2 border-blue-300">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-industry text-blue-600 mr-1"></i>
                                Manufacture Date
                            </label>
                            <input type="date" name="manufacture_date" 
                                   value="{{ old('manufacture_date', $product->manufacture_date?->format('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-times text-red-600 mr-1"></i>
                                Expiry Date
                            </label>
                            <input type="date" name="expiry_date" 
                                   value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-bell text-yellow-600 mr-1"></i>
                                Alert Days Before
                            </label>
                            <input type="number" name="expiry_alert_days" 
                                   value="{{ old('expiry_alert_days', $product->expiry_alert_days ?? 30) }}" 
                                   min="1" max="365"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t">
                <a href="{{ route('products.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-2"></i>Update Product
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    function toggleExpiryFields(checkbox) {
        const expiryFields = document.getElementById('expiryFields');
        if (checkbox.checked) {
            expiryFields.classList.remove('hidden');
        } else {
            expiryFields.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const trackExpiryCheckbox = document.getElementById('track_expiry');
        if (trackExpiryCheckbox && trackExpiryCheckbox.checked) {
            toggleExpiryFields(trackExpiryCheckbox);
        }
    });
</script>
@endpush
@endsection