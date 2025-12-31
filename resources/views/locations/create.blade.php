@extends('layouts.app')

@section('title', 'Create Location')

@section('page-title')
    <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>Add New Location
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    
    <div class="bg-white rounded-xl shadow-lg p-8">
        
        <form action="{{ route('locations.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Location Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-building text-indigo-600 mr-2"></i>Location Name *
                </label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       placeholder="e.g., Main Store, Kampala Branch, Entebbe Warehouse"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror" 
                       required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Give this location a clear name (e.g., city name, store type)</p>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-pin text-indigo-600 mr-2"></i>Address
                </label>
                <textarea name="address" placeholder="e.g., 123 Main Street, Kampala, Uganda"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('address') border-red-500 @enderror"
                          rows="3"></textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-phone text-indigo-600 mr-2"></i>Contact Phone
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}" 
                       placeholder="e.g., +256701234567"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" checked
                       class="h-4 w-4 border-gray-300 rounded">
                <label class="ml-2 text-sm font-medium text-gray-700">
                    <i class="fas fa-check-circle text-green-600 mr-1"></i>Active Location
                </label>
                <p class="text-gray-500 text-xs ml-2">(Inactive locations won't show in POS)</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('locations.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-2"></i>Create Location
                </button>
            </div>
        </form>

    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <i class="fas fa-lightbulb text-blue-600 mr-2"></i>
        <span class="text-sm text-blue-700">
            <strong>Tip:</strong> After creating a location, you can assign staff members to it in Staff Management, and products will be automatically tracked in inventory for this location.
        </span>
    </div>

</div>
@endsection
