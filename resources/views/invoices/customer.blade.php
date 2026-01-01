@extends('layouts.app')
@section('title',"Customer: $customer->name")
@section('content')
@php
    $totalOutstanding = $outstandingInvoices->sum(fn($inv) => $inv->total - $inv->paid);
@endphp
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-gray-100 rounded-md shadow p-6 mb-6 flex flex-col md:flex-row justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold mb-1">{{ $customer->name }}</h2>
            <span class="inline-block px-3 py-2 bg-yellow-200 text-yellow-900 font-bold rounded text-lg mt-1">
                Total Balances: UGX {{ number_format($totalOutstanding) }}
            </span>
            <div class="mt-2 text-sm text-gray-600">
                @if($customer->phone)
                    <strong>Phone:</strong> {{ $customer->phone }}
                @endif
                @if($customer->email)
                    <span class="ml-4"><strong>Email:</strong> {{ $customer->email }}</span>
                @endif
                @if($customer->address)
                    <span class="ml-4"><strong>Address:</strong> {{ $customer->address }}</span>
                @endif
            </div>
        </div>
        <div class="flex gap-3 items-center mt-4 md:mt-0">
            <button onclick="showTab('outstanding')" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-700">Outstanding Invoices</button>
            <button onclick="showTab('cleared')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-800">Cleared Invoices</button>
            <button onclick="showTab('history')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800">Payment History</button>
        </div>
    </div>

    <div id="tab-outstanding" class="tab-content">
        <h3 class="text-xl font-semibold mb-2">Outstanding Invoices</h3>
        @if($outstandingInvoices->count())
        <table class="w-full table-auto bg-white border rounded mb-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    @if(Auth::user()->can('viewAny', \App\Models\Invoice::class))
                        <th>Created By</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($outstandingInvoices as $inv)
                  <tr>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->created_at->format('d M Y') }}</td>
                    <td>{{ number_format($inv->total) }}</td>
                    <td class="text-green-700">{{ number_format($inv->paid) }}</td>
                    <td class="text-red-700 font-bold">{{ number_format($inv->total - $inv->paid) }}</td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700 font-bold">{{ strtoupper($inv->status) }}</span>
                    </td>
                    @if(Auth::user()->can('viewAny', \App\Models\Invoice::class))
                        <td>{{ $inv->user->name ?? '-' }}</td>
                    @endif
                  </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div>No outstanding invoices.</div>
        @endif
    </div>

    <div id="tab-cleared" class="tab-content hidden">
        <h3 class="text-xl font-semibold mb-2">Cleared Invoices</h3>
        @if($paidInvoices->count())
        <table class="w-full table-auto bg-white border rounded mb-4">
            <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Status</th>
                @if(Auth::user()->can('viewAny', \App\Models\Invoice::class))
                    <th>Created By</th>
                @endif
            </tr>
            </thead>
            <tbody>
                @foreach($paidInvoices as $inv)
                  <tr>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->created_at->format('d M Y') }}</td>
                    <td>{{ number_format($inv->total) }}</td>
                    <td class="text-green-700">{{ number_format($inv->paid) }}</td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700 font-bold">PAID</span>
                    </td>
                    @if(Auth::user()->can('viewAny', \App\Models\Invoice::class))
                        <td>{{ $inv->user->name ?? '-' }}</td>
                    @endif
                  </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div>No cleared invoices.</div>
        @endif
    </div>

    <div id="tab-history" class="tab-content hidden">
        <h3 class="text-xl font-semibold mb-2">Payment History</h3>
        @if($payments->count())
        <table class="w-full table-auto bg-white border rounded mb-4">
            <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th>Amount Paid</th>
                <th>Status</th>
                @if(Auth::user()->can('viewAny', \App\Models\Invoice::class))
                    <th>Received By</th>
                @endif
            </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($p->paid_at)->format('d M Y H:i') }}</td>
                    <td>{{ optional($p->invoice)->invoice_number }}</td>
                    <td class="text-indigo-700">{{ number_format($p->amount_paid) }}</td>
                    <td>
                      <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">{{ strtoupper(optional($p->invoice)->status) }}</span>
                    </td>
                    @if(Auth::user()->can('viewAny', \App\Models\Invoice::class))
                        <td>{{ $p->user->name ?? '-' }}</td>
                    @endif
                  </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div>No payment history.</div>
        @endif
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + tab).classList.remove('hidden');
}
showTab('outstanding');
</script>
@endsection