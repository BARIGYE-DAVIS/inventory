@extends('layouts.app')

@section('title', 'Period Closing History')
@section('page-title', 'Inventory Period History')

@section('content')
<div class="grid grid-cols-1 gap-6">
  <div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
      <i class="fas fa-history text-purple-600 mr-2"></i>Period Closing History
    </h2>

    @if($periods->isEmpty())
      <div class="text-center py-12">
        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500">No periods have been closed yet.</p>
        <p class="text-sm text-gray-400 mt-2">Periods are automatically closed at 11:59 PM on the last day of each month.</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Product</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Period</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-700">Opening</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-700">Purchases</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-700">Sales</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-700">Adjustments</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-700">Calculated</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-700">Physical</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-700">Variance</th>
              <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($periods as $period)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-800 font-medium">
                  <a href="{{ route('inventory.show', $period->product_id) }}" class="text-indigo-600 hover:underline">
                    {{ $period->product->name }}
                  </a>
                </td>
                <td class="px-4 py-3 text-gray-600">
                  {{ $period->period_start->format('M d, Y') }} - {{ $period->period_end->format('M d, Y') }}
                </td>
                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($period->opening_stock, 2) }}</td>
                <td class="px-4 py-3 text-right text-green-600 font-semibold">+{{ number_format($period->purchases, 2) }}</td>
                <td class="px-4 py-3 text-right text-red-600 font-semibold">-{{ number_format($period->sales, 2) }}</td>
                <td class="px-4 py-3 text-right font-semibold">
                  @if($period->adjustments > 0)
                    <span class="text-amber-600">+{{ number_format($period->adjustments, 2) }}</span>
                  @elseif($period->adjustments < 0)
                    <span class="text-orange-600">{{ number_format($period->adjustments, 2) }}</span>
                  @else
                    <span class="text-gray-500">0</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right text-gray-700 font-semibold">{{ number_format($period->calculated_stock, 2) }}</td>
                <td class="px-4 py-3 text-right text-gray-700 font-semibold">
                  @if($period->physical_count)
                    {{ number_format($period->physical_count, 2) }}
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right font-semibold">
                  @if($period->variance == 0)
                    <span class="badge bg-green-100 text-green-700">✓ Match</span>
                  @elseif($period->variance > 0)
                    <span class="badge bg-amber-100 text-amber-700">
                      <i class="fas fa-arrow-up mr-1"></i>{{ number_format($period->variance, 2) }}
                    </span>
                  @else
                    <span class="badge bg-red-100 text-red-700">
                      <i class="fas fa-arrow-down mr-1"></i>{{ number_format(abs($period->variance), 2) }}
                    </span>
                  @endif
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="badge bg-blue-100 text-blue-700">
                    <i class="fas fa-lock mr-1"></i>{{ ucfirst($period->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                  No periods found
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Summary Statistics -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
          <p class="text-gray-600 text-sm">Total Periods Closed</p>
          <p class="text-2xl font-bold text-blue-600 mt-1">{{ $periods->count() }}</p>
        </div>
        
        <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
          <p class="text-gray-600 text-sm">Perfect Matches</p>
          <p class="text-2xl font-bold text-green-600 mt-1">{{ $periods->where('variance', 0)->count() }}</p>
        </div>
        
        <div class="bg-amber-50 rounded-lg p-4 border-l-4 border-amber-500">
          <p class="text-gray-600 text-sm">Total Overstock</p>
          <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($periods->where('variance', '>', 0)->sum('variance'), 0) }}</p>
        </div>
        
        <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-500">
          <p class="text-gray-600 text-sm">Total Shortage</p>
          <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format(abs($periods->where('variance', '<', 0)->sum('variance')), 0) }}</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        {{ $periods->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
