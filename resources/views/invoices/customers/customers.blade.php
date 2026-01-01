@extends('layouts.app')
@section('title', 'Customers with Invoices')
@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Customers with Invoices</h1>
    <table class="min-w-full bg-white border border-gray-200 rounded">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">Phone</th>
                <th class="px-3 py-2 text-right">Outstanding (UGX)</th>
                <th class="px-3 py-2 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($customers as $customer)
            @php
                $outstanding = $customer->invoices->sum(fn($inv) => ($inv->status != 'paid') ? ($inv->total - $inv->paid) : 0);
            @endphp
            <tr>
                <td class="px-3 py-2 border-b">{{ $customer->name }}</td>
                <td class="px-3 py-2 border-b">{{ $customer->phone }}</td>
                <td class="px-3 py-2 border-b text-right {{ $outstanding > 0 ? 'text-red-600 font-bold' : 'text-green-600' }}">
                    {{ number_format($outstanding) }}
                </td>
                <td class="px-3 py-2 border-b text-center">
                    <a href="{{ route('invoices.customerSummary', $customer->id) }}" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-800">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center py-4">No customers with invoices.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection