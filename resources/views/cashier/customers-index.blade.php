@extends('layouts.cashier-layout')

@section('title', 'Customers')

@section('page-title')
    <i class="fas fa-users text-green-600 mr-2"></i>Customers
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Search & Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <form method="GET" action="{{ route('customers.index') }}" class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search customers by name, phone, or email..." 
                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                </form>
            </div>
            <a href="{{ route('customers.create') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 whitespace-nowrap">
                <i class="fas fa-user-plus mr-2"></i>Add Customer
            </a>
        </div>
    </div>

    <!-- Customers List -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-list text-green-600 mr-2"></i>All Customers ({{ $customers->total() }})
        </h3>

        @if($customers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($customers as $customer)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-lg font-bold text-green-600">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold text-gray-900">{{ $customer->name }}</h4>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-shopping-bag mr-1"></i>
                                {{ $customer->sales->count() }} purchases
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <p class="text-gray-600">
                        <i class="fas fa-phone text-green-600 mr-2"></i>{{ $customer->phone }}
                    </p>
                    @if($customer->email)
                    <p class="text-gray-600">
                        <i class="fas fa-envelope text-green-600 mr-2"></i>{{ $customer->email }}
                    </p>
                    @endif
                    @if($customer->address)
                    <p class="text-gray-600">
                        <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>{{ $customer->address }}
                    </p>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                    <span class="text-sm font-semibold text-green-600">
                        Total: UGX {{ number_format($customer->sales->sum('total'), 0) }}
                    </span>
                    <a href="{{ route('customers.show', $customer->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-eye"></i> View
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $customers->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-users-slash text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No customers found</p>
            @if(request('search'))
            <p class="text-gray-400 text-sm mt-2">Try a different search term</p>
            <a href="{{ route('customers.index') }}" class="inline-block mt-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-times mr-2"></i>Clear Search
            </a>
            @else
            <a href="{{ route('customers.create') }}" class="inline-block mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-user-plus mr-2"></i>Add Your First Customer
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection