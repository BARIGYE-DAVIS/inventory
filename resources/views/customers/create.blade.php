@extends('layouts.app')

@section('title', 'Add Customer')

@section('page-title')
    <i class="fas fa-user-plus text-indigo-600 mr-2"></i>Add New Customer
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6">
        
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-indigo-600 mr-1"></i>
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="John Doe">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone text-indigo-600 mr-1"></i>
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="0700123456">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-indigo-600 mr-1"></i>
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="customer@email.com">
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-indigo-600 mr-1"></i>
                        Address
                    </label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Kampala, Uganda">
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-city text-indigo-600 mr-1"></i>
                        City
                    </label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Kampala">
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag text-indigo-600 mr-1"></i>
                        Country
                    </label>
                    <input type="text" name="country" value="{{ old('country', 'Uganda') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Uganda">
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t">
                <a href="{{ route('customers.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-2"></i>Save Customer
                </button>
            </div>
        </form>

    </div>
</div>
@endsection