@extends('layouts.cashier-layout')

@section('title', 'Cashier Invoices')

@section('page-title')
    <i class="fas fa-file-invoice-dollar text-indigo-600 mr-2"></i>
    Invoices
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow mb-6 p-4 flex flex-col md:flex-row items-center justify-between gap-2">
        <div>
            <form method="GET" class="flex gap-3" id="filterForm" action="{{ route('cashier.invoices.index') }}">
                <select name="status" id="statusSelect" onchange="fetchInvoices()" class="px-4 py-2 border rounded-md focus:ring-indigo-600 focus:border-indigo-600">
                    <option value="">All</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                <input name="search" id="searchInput" type="text" value="{{ request('search') }}" placeholder="Search Invoice # / Customer" class="px-4 py-2 border rounded-md focus:ring-indigo-600 focus:border-indigo-600" autocomplete="off" />
            </form>
        </div>
        
    </div>

    <!-- Invoice List Table -->
    <div class="overflow-x-auto bg-white rounded-xl shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Invoice #</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Customer</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-600 uppercase">Total</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="invoice-table-body">
                @forelse($invoices as $i => $invoice)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $loop->iteration + ($invoices->currentPage() - 1)*$invoices->perPage() }}</td>
                        <td class="px-4 py-2 text-sm font-bold text-indigo-700">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2 text-sm">{{ $invoice->customer->name ?? 'Walk-in' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold">UGX {{ number_format($invoice->total, 0) }}</td>
                        <td class="px-4 py-2 text-center">
                            @if($invoice->status === 'paid')
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">PAID</span>
                            @else
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{ strtoupper($invoice->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('cashier.invoices.show', $invoice->id) }}"
                               class="inline-block px-3 py-1 text-xs rounded bg-indigo-100 text-indigo-700 font-bold hover:bg-indigo-200">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('cashier.invoices.print', $invoice->id) }}"
                               class="inline-block px-3 py-1 text-xs rounded bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold" target="_blank">
                                <i class="fas fa-print"></i>
                                {{ $invoice->status === 'paid' ? 'Receipt' : 'Print' }}
                            </a>
                            @if($invoice->status !== 'paid')
                                <form action="{{ route('cashier.invoices.markPaid', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Mark this invoice as PAID?');">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1 text-xs rounded bg-green-100 text-green-700 font-bold hover:bg-green-200">
                                        <i class="fas fa-coins"></i> Mark Paid
                                    </button>
                                </form>
                            @endif
                            @if($invoice->status === 'paid')
                                <form action="{{ route('cashier.invoices.destroy', $invoice->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this invoice? This cannot be undone!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 text-xs rounded bg-red-100 text-red-700 font-bold hover:bg-red-200 ml-0.5">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-8">
                            <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                            <div>No invoices found.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="my-6" id="pagination-links">
        {{ $invoices->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
// Debounce utility
let searchTimeout;
const form = document.getElementById('filterForm');
const searchInput = document.getElementById('searchInput');
const statusSelect = document.getElementById('statusSelect');
const tableBody = document.getElementById('invoice-table-body');
const pagLinksDiv = document.getElementById('pagination-links');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(fetchInvoices, 300);
});

statusSelect.addEventListener('change', fetchInvoices);

function fetchInvoices(page = 1) {
    let params = new URLSearchParams(new FormData(form));
    params.set('page', page);
    fetch(form.action + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(res => res.text()).then(html => {
        // Extract <tbody> & pagination from response
        let tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const newTbody = tempDiv.querySelector('#invoice-table-body');
        const newPagLinks = tempDiv.querySelector('#pagination-links');

        if (newTbody) tableBody.innerHTML = newTbody.innerHTML;
        if (newPagLinks) pagLinksDiv.innerHTML = newPagLinks.innerHTML;

        bindPaginationLinks();
    });
}

// Pagination links (after AJAX)
function bindPaginationLinks() {
    let pagLinks = pagLinksDiv.querySelectorAll('a');
    pagLinks.forEach(a => {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(a.href);
            const page = url.searchParams.get('page');
            fetchInvoices(page);
        });
    });
}
// Initial call in case paginated
bindPaginationLinks();
</script>
@endpush