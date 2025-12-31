@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h2 class="text-3xl font-bold mb-6 text-blue-700">Invoices</h2>
    {{-- Filter Tabs --}}
    <div class="flex gap-3 mb-6 p-3 bg-gray-100 rounded-lg m-4">
        <a href="{{ route('invoices.paid') }}"
           class="px-4 py-2 rounded-lg {{ $status === 'paid' ? 'bg-green-600 text-white font-semibold' : 'bg-gray-200 m-5 hover:bg-gray-300 text-gray-800' }}">
            Paid
        </a>
        <a href="{{ route('invoices.unpaid') }}"
           class="px-4 py-2 rounded-lg {{ $status === 'unpaid' ? 'bg-yellow-600 text-white font-semibold' : 'bg-gray-200 hover:bg-gray-300 text-gray-800' }}">
            Unpaid
        </a>
    </div>
    <div class="mb-4">
        <input
            type="text"
            id="liveSearchInput"
            class="border rounded-md px-4 py-2 w-80 focus:ring focus:ring-blue-200"
            placeholder="Live search invoice or customer...">
    </div>
    {{-- Notifications --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded-md">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="bg-red-100 text-red-800 p-3 mb-4 rounded-md">{{ session('error') }}</div>
    @elseif (session('info'))
        <div class="bg-blue-50 text-blue-900 p-3 mb-4 rounded-md">{{ session('info') }}</div>
    @endif

    {{-- Table results --}}
    <div id="invoicesTable">
        @include('invoices.partials.table', ['invoices' => $invoices])
    </div>
</div>
{{-- Alpine.js (if you use it elsewhere) and vanilla JS for AJAX live search --}}
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('liveSearchInput');
        const invoicesTable = document.getElementById('invoicesTable');
        let timeout = null;

        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                let search = input.value;
                let url = '{{ $status === "paid" ? route("invoices.paid") : route("invoices.unpaid") }}';
                fetch(url + '?search=' + encodeURIComponent(search), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => { invoicesTable.innerHTML = data.html; });
            }, 350);
        });
    });
</script>
@endsection