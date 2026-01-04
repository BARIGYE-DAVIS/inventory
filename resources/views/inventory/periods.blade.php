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
              <th class="px-4 py-3 text-left font-semibold text-gray-700 w-8"></th>
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
              <th class="px-4 py-3 text-center font-semibold text-gray-700">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($periods as $period)
              <tr class="hover:bg-gray-50 cursor-pointer" onclick="toggleRow({{ $period->id }})">
                <td class="px-4 py-3 text-center">
                  <i class="fas fa-chevron-down transition-transform duration-200" id="chevron-{{ $period->id }}"></i>
                </td>
                <td class="px-4 py-3 text-gray-800 font-medium">
                  <a href="{{ route('inventory.show', $period->product_id) }}" class="text-indigo-600 hover:underline" onclick="event.stopPropagation()">
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
                <td class="px-4 py-3 text-center">
                  <a href="{{ route('inventory.reconciliation', $period->id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline" onclick="event.stopPropagation()">
                    <i class="fas fa-eye mr-1"></i>View
                  </a>
                </td>
              </tr>
              
              <!-- Expandable Detail Row -->
              <tr id="detail-{{ $period->id }}" class="hidden">
                <td colspan="12" class="px-4 py-6 bg-gray-50">
                  <div class="space-y-4">
                    <!-- Reconciliation Breakdown -->
                    <div class="bg-white rounded-lg p-6 border border-gray-200">
                      <h4 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-balance-scale text-indigo-600 mr-2"></i>Stock Reconciliation
                      </h4>
                      
                      <!-- Step 1: System Calculation -->
                      <div class="mb-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Step 1: System Calculated Stock</p>
                        <div class="space-y-2 text-sm">
                          <div class="flex justify-between items-center">
                            <span class="text-gray-700">Opening Stock (from products.opening_stock)</span>
                            <span class="font-semibold text-blue-600">{{ number_format($period->opening_stock, 2) }} units</span>
                          </div>
                          <div class="flex justify-between items-center">
                            <span class="text-gray-700">+ Purchases (sum of all purchase_items)</span>
                            <span class="font-semibold text-green-600">+{{ number_format($period->purchases, 2) }} units</span>
                          </div>
                          <div class="flex justify-between items-center">
                            <span class="text-gray-700">- Sales (sum of all sale_items)</span>
                            <span class="font-semibold text-red-600">-{{ number_format($period->sales, 2) }} units</span>
                          </div>
                          <div class="border-t pt-2 mt-2 font-bold text-base">
                            <div class="flex justify-between items-center">
                              <span class="text-gray-800">= System Calculated Stock</span>
                              <span class="text-indigo-600">{{ number_format($period->calculated_stock, 2) }} units</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Step 2: Physical Count & Variance -->
                      @if($period->physical_count)
                      <div class="mb-6 p-4 {{ $period->variance < 0 ? 'bg-red-50 border-l-4 border-red-500' : 'bg-green-50 border-l-4 border-green-500' }} rounded-lg">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Step 2: Physical Count & Variance</p>
                        <div class="space-y-2 text-sm">
                          <div class="flex justify-between items-center">
                            <span class="text-gray-700">Physical Count (from stock_adjustments)</span>
                            <span class="font-semibold {{ $period->variance < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($period->physical_count, 2) }} units</span>
                          </div>
                          <div class="flex justify-between items-center">
                            <span class="text-gray-700">- System Calculated Stock</span>
                            <span class="font-semibold text-indigo-600">{{ number_format($period->calculated_stock, 2) }} units</span>
                          </div>
                          <div class="border-t pt-2 mt-2 font-bold text-base">
                            <div class="flex justify-between items-center">
                              <span class="text-gray-800">= Variance ({{ $period->variance < 0 ? 'Loss' : 'Gain' }})</span>
                              <span class="{{ $period->variance < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $period->variance >= 0 ? '+' : '' }}{{ number_format($period->variance, 2) }} units 
                                <span class="text-xs">({{ number_format($period->variance_percentage, 2) }}%)</span>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Step 3: Final Reconciliation -->
                      <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Step 3: Final Reconciliation</p>
                        <div class="space-y-2 text-sm">
                          <div class="flex justify-between items-center">
                            <span class="text-gray-700">Reconciliation Adjustment</span>
                            <span class="font-semibold {{ $period->variance < 0 ? 'text-red-600' : 'text-green-600' }}">
                              {{ $period->variance >= 0 ? '+' : '' }}{{ number_format($period->variance, 2) }} units
                            </span>
                          </div>
                          <div class="border-t pt-2 mt-2 font-bold text-base">
                            <div class="flex justify-between items-center">
                              <span class="text-gray-800">= Final Accepted Stock</span>
                              <span class="text-purple-600">{{ number_format($period->physical_count, 2) }} units</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      @else
                      <div class="p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                        <p class="text-sm text-yellow-800">
                          <i class="fas fa-exclamation-triangle mr-2"></i>Physical count not yet recorded. 
                          System calculated stock ({{ number_format($period->calculated_stock, 2) }} units) is being used.
                        </p>
                      </div>
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="12" class="px-4 py-8 text-center text-gray-500">
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

<script>
  function toggleRow(periodId) {
    const detailRow = document.getElementById('detail-' + periodId);
    const chevron = document.getElementById('chevron-' + periodId);
    
    detailRow.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
  }
</script>
