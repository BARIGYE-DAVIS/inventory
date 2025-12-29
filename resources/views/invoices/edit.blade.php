{{-- resources/views/invoices/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Invoice')

@section('content')
@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        <ul class="list-disc ml-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-5xl mx-auto mb-8">
    <div class="bg-white rounded-xl shadow-md p-4 mb-3">
        <div class="flex flex-wrap items-center gap-8 mb-2">
            <div>
                <div class="font-bold text-blue-700 text-lg mb-1">Invoice #{{ $invoice->invoice_number }}</div>
                <div class="text-gray-900"><strong>Customer:</strong> {{ $invoice->customer->name ?? 'Unknown' }}</div>
                <div class="text-gray-700"><strong>Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</div>
            </div>
            <div>
                <div class="text-gray-700"><strong>Notes:</strong> {{ $invoice->notes ?? '-' }}</div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs bg-gray-50 rounded">
                <thead>
                <tr>
                    <th class="px-2 py-1 text-left">Product</th>
                    <th class="px-2 py-1 text-right">Qty</th>
                    <th class="px-2 py-1 text-right">Unit</th>
                    <th class="px-2 py-1 text-right">Price</th>
                    <th class="px-2 py-1 text-right">Disc</th>
                    <th class="px-2 py-1 text-right">Line Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($invoice->items as $i)
                    <tr>
                        <td class="px-2 py-1">{{ $i->description ?? ($i->product->name ?? '-') }}</td>
                        <td class="px-2 py-1 text-right">{{ $i->quantity }}</td>
                        <td class="px-2 py-1 text-right">{{ $i->product->unit ?? '-' }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format($i->unit_price, 2) }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format($i->discount ?? 0, 2) }}</td>
                        <td class="px-2 py-1 text-right">{{ number_format(($i->unit_price - ($i->discount ?? 0)) * $i->quantity, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-gray-400">No items</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right font-bold">Total:</td>
                        <td class="text-right font-bold text-blue-700">{{ number_format($invoice->total,2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- SEE HISTORY BUTTON --}}
        @if(isset($history) && $history->count())
        <button type="button" onclick="toggleHistory()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800">
            <i class="fas fa-history mr-1"></i> See Invoice History
        </button>
        @endif
    </div>
</div>

{{-- HIDDEN HISTORY BLOCK --}}
@if(isset($history) && $history->count())
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-5 mb-10" id="historyBlock" style="display:none;">
    <h2 class="text-lg font-bold mb-2 text-blue-800 flex items-center">
        <i class="fas fa-history mr-2"></i> Change History
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-xs border">
            <thead>
                <tr>
                    <th class="px-2 py-1 text-left">Edited At</th>
                    <th class="px-2 py-1 text-left">Edited By</th>
                    <th class="px-2 py-1 text-left">Customer</th>
                    <th class="px-2 py-1 text-left">Items</th>
                    <th class="px-2 py-1 text-right">Subtotal</th>
                    <th class="px-2 py-1 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
            @foreach($history as $log)
                @php $snap = json_decode($log->snapshot, true); @endphp
                <tr>
                    <td class="px-2 py-1">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                    <td class="px-2 py-1">{{ $log->edited_by ?? '-' }}</td>
                    <td class="px-2 py-1">{{ $snap['customer'] ?? '-' }}</td>
                    <td class="px-2 py-1">
                        <ul class="list-disc ml-4">
                        @foreach(($snap['items'] ?? []) as $row)
                            <li>{{ $row['product_name'] ?? $row['product_id'] }} ({{ $row['quantity'] }} × {{ number_format($row['price'],0) }})</li>
                        @endforeach
                        </ul>
                    </td>
                    <td class="px-2 py-1 text-right">{{ number_format($snap['subtotal']??0,0) }}</td>
                    <td class="px-2 py-1 text-right">{{ number_format($snap['total']??0,0) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- The rest of your main POS edit JS and live cart UI goes below --}}

<script>
function toggleHistory() {
    var hb = document.getElementById('historyBlock');
    if(hb.style.display === 'none') { hb.style.display = ''; }
    else { hb.style.display = 'none'; }
}
</script>
@endsection