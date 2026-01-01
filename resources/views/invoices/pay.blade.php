@extends('layouts.app')

@section('title', 'Pay Invoice - ' . $invoice->invoice_number)

@section('page-title')
    <i class="fas fa-coins text-green-600 mr-2"></i>
    Pay Invoice - {{ $invoice->invoice_number }}
@endsection

@section('content')
<div class="max-w-3xl mx-auto my-8">

    <!-- Invoice Overview with Customer/Business/Creator -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-700 mb-2 flex items-center gap-2">
            <span>Invoice #{{ $invoice->invoice_number }}</span>
            <span class="text-xs font-normal px-2 rounded
                @if($invoice->status==='paid') bg-green-100 text-green-700
                @elseif($invoice->status==='partial') bg-yellow-100 text-yellow-700
                @else bg-gray-200 text-gray-700 @endif">
                {{ strtoupper($invoice->status) }}
            </span>
        </h2>
        <div class="mb-2 text-gray-600">Date Issued: {{ $invoice->created_at->format('d M Y h:iA') }}</div>
        <div class="mb-1 text-gray-600">Business: <strong>{{ $invoice->business->name ?? '-' }}</strong></div>
        <div class="mb-1 text-gray-600">
            Customer: <strong>{{ $invoice->customer->name ?? 'Walk-in' }}</strong>
            @if($invoice->customer && $invoice->customer->phone)
                | 📞 {{ $invoice->customer->phone }}
            @endif
            @if($invoice->customer && $invoice->customer->email)
                | ✉️ {{ $invoice->customer->email }}
            @endif
        </div>
        @if($invoice->customer && $invoice->customer->address)
            <div class="mb-1 text-gray-600">Address: {{ $invoice->customer->address }}</div>
        @endif
        <div class="mb-1 text-gray-600">
            <strong>Created By:</strong> {{ $invoice->user->name ?? '---' }}
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto my-4">
            <table class="min-w-full bg-white border border-gray-200 rounded">
                <thead>
                    <tr>
                        <th class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600 text-left">#</th>
                        <th class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600 text-left">Description</th>
                        <th class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600 text-right">Qty</th>
                        <th class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600 text-right">Unit Price</th>
                        <th class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600 text-right">Discount</th>
                        <th class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($invoice->items as $i => $item)
                    <tr>
                        <td class="px-3 py-2 border-b">{{ $i+1 }}</td>
                        <td class="px-3 py-2 border-b">
                            <div class="font-medium">{{ $item->description ?? ($item->product->name ?? '-') }}</div>
                            @if($item->product && $item->product->sku)
                                <div class="text-xs text-gray-400">{{ $item->product->sku }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 border-b text-right">{{ $item->quantity }}</td>
                        <td class="px-3 py-2 border-b text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-3 py-2 border-b text-right">{{ number_format($item->discount ?? 0, 2) }}</td>
                        <td class="px-3 py-2 border-b text-right font-semibold">
                            {{ number_format(($item->unit_price - ($item->discount ?? 0)) * $item->quantity, 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="mt-4 w-full md:w-1/2 ml-auto">
            <div class="flex justify-between py-1 text-gray-700">
                <span>Subtotal:</span>
                <span>UGX {{ number_format($invoice->subtotal,2) }}</span>
            </div>
            <div class="flex justify-between py-1 text-red-500">
                <span>Discount:</span>
                <span>
                    @if($invoice->discount_amount > 0)
                        -UGX {{ number_format($invoice->discount_amount,2) }}
                    @else
                        UGX 0
                    @endif
                </span>
            </div>
            @if($invoice->tax_amount > 0)
            <div class="flex justify-between py-1 text-gray-700">
                <span>Tax (18%):</span>
                <span>UGX {{ number_format($invoice->tax_amount,2) }}</span>
            </div>
            @endif
            <div class="flex justify-between py-2 mt-2 border-t-2 font-bold text-lg">
                <span>TOTAL:</span>
                <span>UGX {{ number_format($invoice->total,2) }}</span>
            </div>
            <div class="flex justify-between py-1 text-green-600 font-semibold">
                <span>Paid:</span>
                <span>UGX {{ number_format($invoice->paid,2) }}</span>
            </div>
            <div class="flex justify-between py-1 text-red-600 font-semibold">
                <span>Balance:</span>
                <span>UGX {{ number_format($invoice->total - $invoice->paid,2) }}</span>
            </div>
        </div>

        @if($invoice->notes)
            <div class="mt-5 p-3 bg-gray-50 rounded">
                <div class="font-semibold text-gray-600 mb-1">Notes:</div>
                <div class="text-gray-800 text-sm">{{ $invoice->notes }}</div>
            </div>
        @endif
    </div>

    <!-- Payment Form -->
    @if($invoice->status !== 'paid')
    <div class="bg-gray-50 rounded-xl shadow-inner p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-3">Make a Payment</h2>
        <form method="POST" action="{{ route('invoices.pay', $invoice->id) }}">
            @csrf
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="payment_type" value="full" 
                        {{ old('payment_type', 'full') === 'full' ? 'checked' : '' }} 
                        class="mr-2"> Full Payment
                </label>
                <label class="inline-flex items-center ml-4">
                    <input type="radio" name="payment_type" value="partial" 
                        {{ old('payment_type') === 'partial' ? 'checked' : '' }} 
                        class="mr-2"> Partial Payment
                </label>
            </div>
            <input type="hidden" name="amount" id="fullPaymentAmountInput" value="{{ $invoice->total - $invoice->paid }}">
            <!-- This input only appears for Partial Payment -->
            <div id="partialAmountDiv" class="mb-4 hidden">
                <label for="partialPaymentInput" class="block text-gray-700 mb-1">Amount to pay (UGX):</label>
                <input type="number" min="1" max="{{ $invoice->total - $invoice->paid }}" name="amount"
                    id="partialPaymentInput" class="w-full border rounded px-3 py-2"
                    value="{{ old('amount') }}"
                />
                <div class="text-xs text-gray-500 mt-1">Outstanding: UGX {{ number_format($invoice->total - $invoice->paid) }}</div>
                <div class="text-xs text-blue-600 font-semibold mt-1" id="liveBalance" style="display:none;">
                    New Balance: <span id="calculatedBalance"></span>
                </div>
                @error('amount')<div class="text-xs text-red-500">{{ $message }}</div>@enderror
            </div>
            <div class="flex gap-3 justify-end">
                <a href="{{ route('invoices.show', $invoice->id) }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Submit Payment</button>
            </div>
        </form>
    </div>
    @else
        <div class="bg-green-50 border border-green-300 rounded-xl p-6 text-center text-green-700 font-semibold">
            This invoice has already been <b>fully paid</b>.
        </div>
    @endif
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.getElementsByName('payment_type');
    const partialDiv = document.getElementById('partialAmountDiv');
    const partialInput = document.getElementById('partialPaymentInput');
    const fullAmountInput = document.getElementById('fullPaymentAmountInput');
    const liveBalanceDiv = document.getElementById('liveBalance');
    const calculatedBalance = document.getElementById('calculatedBalance');
    const outstanding = {{ $invoice->total - $invoice->paid }};

    function checkInitial() {
        const checkedRadio = document.querySelector('input[name="payment_type"]:checked');
        if (checkedRadio && checkedRadio.value === 'partial') {
            partialDiv.classList.remove('hidden');
            if (partialInput) {
                fullAmountInput.disabled = true;
                partialInput.disabled = false;
                partialInput.focus();
                updateLiveBalance();
            }
        } else {
            partialDiv.classList.add('hidden');
            liveBalanceDiv.style.display = 'none';
            if (fullAmountInput) {
                fullAmountInput.disabled = false;
                if (partialInput) partialInput.disabled = true;
            }
        }
    }

    function updateLiveBalance() {
        if (!partialInput) return;
        const value = parseFloat(partialInput.value || 0);
        if (value > 0 && value <= outstanding) {
            calculatedBalance.innerText = 'UGX ' + (outstanding - value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            liveBalanceDiv.style.display = 'block';
        } else {
            liveBalanceDiv.style.display = 'none';
        }
    }

    // Keep hidden input in sync (always have "amount" in POST)
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'partial') {
                partialDiv.classList.remove('hidden');
                if (fullAmountInput) fullAmountInput.disabled = true;
                if (partialInput) {
                    partialInput.disabled = false;
                    partialInput.focus();
                    updateLiveBalance();
                }
            } else {
                partialDiv.classList.add('hidden');
                liveBalanceDiv.style.display = 'none';
                if (fullAmountInput) fullAmountInput.disabled = false;
                if (partialInput) partialInput.disabled = true;
            }
        });
    });

    // Auto-fill hidden or partial input before submitting the form (for safety)
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedRadio = document.querySelector('input[name="payment_type"]:checked');
        if (checkedRadio.value === 'full') {
            if (fullAmountInput) fullAmountInput.value = outstanding;
        } else if (partialInput) {
            if (fullAmountInput) fullAmountInput.value = '';
        }
    });

    if (partialInput) {
        partialInput.addEventListener('input', updateLiveBalance);
        partialInput.addEventListener('change', updateLiveBalance);
    }

    // Call initial setup
    checkInitial();
});
</script>