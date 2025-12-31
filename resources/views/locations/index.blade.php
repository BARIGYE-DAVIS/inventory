@extends('layouts.app')

@section('title', 'Manage Locations')

@section('page-title')
    <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>Manage Locations / Branches
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Your Locations</h2>
        <a href="{{ route('locations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i>Add Location
        </a>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
        <span class="text-sm text-blue-700">
            Create and manage your business locations (main warehouse, branches, stores). Staff members can be assigned to specific locations for inventory tracking.
        </span>
    </div>

    <!-- Locations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($locations as $location)
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $location->name }}</h3>
                    @if($location->is_main)
                    <span class="inline-block mt-1 px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-star mr-1"></i>Main Location
                    </span>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('locations.edit', $location->id) }}" 
                       class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if(!$location->is_main)
                    <form action="{{ route('locations.destroy', $location->id) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Details -->
            <div class="space-y-3">
                @if($location->address)
                <div class="flex items-start">
                    <i class="fas fa-map-pin text-gray-400 mr-3 mt-1 text-sm"></i>
                    <span class="text-sm text-gray-600">{{ $location->address }}</span>
                </div>
                @endif

                @if($location->phone)
                <div class="flex items-center">
                    <i class="fas fa-phone text-gray-400 mr-3"></i>
                    <span class="text-sm text-gray-600">{{ $location->phone }}</span>
                </div>
                @endif

                <!-- Status -->
                <div class="flex items-center pt-2 border-t">
                    <i class="fas fa-circle text-sm mr-2 {{ $location->is_active ? 'text-green-500' : 'text-gray-400' }}"></i>
                    <span class="text-sm font-medium {{ $location->is_active ? 'text-green-700' : 'text-gray-500' }}">
                        {{ $location->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-gray-50 rounded-xl p-8 text-center">
                <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-600 mb-4">No locations created yet</p>
                <a href="{{ route('locations.create') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Create Your First Location
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($locations->hasPages())
    <div class="mt-6">
        {{ $locations->links() }}
    </div>
    @endif

</div>
@endsection
