@extends('layouts.app')

@section('title', 'Stock Taking Session')

@section('page-title')
    <i class="fas fa-list-check text-indigo-600 mr-2"></i>Stock Taking Session
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Session Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Stock Count Session</h2>
                <p class="text-indigo-200">Started: {{ $session->session_date->format('M d, Y \a\t H:i') }} by {{ $session->initiator->name }}</p>
                @if($session->notes)
                <p class="text-indigo-200 mt-2"><strong>Notes:</strong> {{ $session->notes }}</p>
                @endif
            </div>
            <div class="text-right">
                <span class="inline-block px-4 py-2 bg-white text-indigo-600 rounded-full font-bold text-lg">
                    {{ ucfirst($session->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <a href="{{ route('stock-taking.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center space-x-1">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Sessions</span>
    </a>

    <!-- Products Count Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-barcode text-indigo-600 mr-2"></i>Product Count Entry
        </h3>

        <form id="countForm" class="space-y-4">
            @csrf
            <input type="hidden" name="stock_taking_session_id" value="{{ $session->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-box text-indigo-600 mr-1"></i>Product
                    </label>
                    <select name="product_id" id="productSelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" onchange="updateProductInfo()">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-system-qty="{{ $product->quantity }}" data-name="{{ $product->name }}">
                            {{ $product->name }} (Category: {{ $product->category->name ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- System Quantity (Display Only) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-server text-blue-600 mr-1"></i>System Quantity
                    </label>
                    <input type="text" id="systemQty" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700" placeholder="0">
                </div>

                <!-- Physical Count -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-count text-green-600 mr-1"></i>Physical Count <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="physical_count" id="physicalCount" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Enter actual count" onchange="calculateVariance()">
                </div>

                <!-- Variance (Display) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-code-compare text-orange-600 mr-1"></i>Variance
                    </label>
                    <input type="text" id="variance" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700" placeholder="0">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-purple-600 mr-1"></i>Notes (Optional)
                    </label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="e.g., Damaged items, Spoilage, etc."></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t">
                <button type="submit" class="flex items-center space-x-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save"></i>
                    <span>Record Count</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Recorded Counts -->
    @if($adjustments->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-check-circle text-green-600 mr-2"></i>Recorded Counts ({{ $adjustments->count() }})
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">System</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Physical</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Variance</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $adjustment->product->name }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ number_format($adjustment->system_quantity, 2) }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-900">{{ number_format($adjustment->physical_count, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-sm font-semibold
                                @if($adjustment->variance > 0)
                                    bg-green-100 text-green-800
                                @elseif($adjustment->variance < 0)
                                    bg-red-100 text-red-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif">
                                {{ $adjustment->variance > 0 ? '+' : '' }}{{ number_format($adjustment->variance, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                                {{ $adjustment->reason }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($adjustment->notes)
                                <span title="{{ $adjustment->notes }}" class="cursor-help">{{ Str::limit($adjustment->notes, 40) }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Session Actions -->
    <div class="flex gap-4 justify-end">
        @if($session->status === 'active')
        <form method="POST" action="{{ route('stock-taking.close-session', $session->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to close this session? You can still add more counts after closing.');">
            @csrf
            <button type="submit" class="flex items-center space-x-2 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-lock"></i>
                <span>Close Session</span>
            </button>
        </form>
        @endif
    </div>

</div>

<script>
function updateProductInfo() {
    const select = document.getElementById('productSelect');
    const option = select.options[select.selectedIndex];
    const systemQty = option.dataset.systemQty || 0;
    document.getElementById('systemQty').value = systemQty;
    document.getElementById('variance').value = '';
    document.getElementById('physicalCount').value = '';
}

function calculateVariance() {
    const systemQty = parseFloat(document.getElementById('systemQty').value) || 0;
    const physicalCount = parseFloat(document.getElementById('physicalCount').value) || 0;
    const variance = physicalCount - systemQty;
    document.getElementById('variance').value = variance.toFixed(2);
}

document.getElementById('countForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const productSelect = document.getElementById('productSelect');
    const productName = productSelect.options[productSelect.selectedIndex].dataset.name;
    
    // Submit via fetch or standard form
    fetch('{{ route("stock-taking.record-count") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Count recorded for ' + productName);
            this.reset();
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback to standard form submission
        this.submit();
    });
});
</script>
@endsection
