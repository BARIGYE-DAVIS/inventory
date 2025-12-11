@extends('layouts.cashier-layout')

@section('title', 'Add Customer')

@section('page-title')
    <i class="fas fa-user-plus text-green-600 mr-2"></i>Add New Customer
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-4xl text-green-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Add New Customer</h2>
            <p class="text-gray-600 mt-2">Fill in customer details below</p>
        </div>

        <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-green-600 mr-1"></i>
                    Customer Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       placeholder="Enter customer name">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-phone text-green-600 mr-1"></i>
                    Phone Number <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       placeholder="0700123456">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email (Optional) -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-green-600 mr-1"></i>
                    Email Address <span class="text-gray-400 text-xs">(Optional)</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       placeholder="customer@email.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address (Optional) -->
            <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-green-600 mr-1"></i>
                    Address <span class="text-gray-400 text-xs">(Optional)</span>
                </label>
                <textarea id="address" 
                          name="address" 
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Enter customer address">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium">Quick Tip:</p>
                        <ul class="list-disc list-inside mt-1 text-xs space-y-1">
                            <li>Customer name and phone are required</li>
                            <li>Email and address are optional</li>
                            <li>You can add more details later</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="submit" class="flex-1 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold text-lg">
                    <i class="fas fa-check-circle mr-2"></i>Save Customer
                </button>
                <a href="{{ route('customers.index') }}" class="flex-1 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold text-lg text-center">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection