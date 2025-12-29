<table class="w-full table-auto border bg-white text-sm rounded-md shadow">
    <thead class="bg-gray-100">
    <tr>
        <th class="p-2 text-left">Invoice #</th>
        <th class="p-2 text-left">Customer</th>
        <th class="p-2 text-left">Date</th>
        <th class="p-2 text-right">Total</th>
        <th class="p-2 text-center">Status</th>
        <th class="p-2 text-center">Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($invoices as $invoice)
        <tr>
            <td class="p-2">{{ $invoice->invoice_number }}</td>
            <td class="p-2">{{ $invoice->customer->name ?? '-' }}</td>
            <td class="p-2">{{ $invoice->created_at->format('Y-m-d') }}</td>
            <td class="p-2 text-right">{{ number_format($invoice->total,2) }}</td>
            <td class="p-2 text-center">
                @if($invoice->status === 'paid')
                    <span class="rounded bg-green-500 text-white px-2 py-1">Paid</span>
                @else
                    <span class="rounded bg-yellow-400 text-white px-2 py-1">Unpaid</span>
                @endif
            </td>
            <td class="p-2 text-center space-x-1">
                <a href="{{ route('invoices.show', $invoice->id) }}"
                  class="inline-block px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-800" title="View">
                  <i class="fas fa-eye"></i>
                </a>
               
                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
                      style="display: inline"
                      onsubmit="return confirm('Delete this invoice? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="inline-block px-3 py-1 bg-red-600 text-white rounded hover:bg-red-800" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                </form>

                 <form action="{{ route('invoices.markPaid', $invoice->id) }}" method="POST" class="inline" onsubmit="return confirm('Mark this invoice as paid?');">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs">Mark Paid</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
          <td colspan="6" class="text-center text-gray-400 p-5">No invoices found.</td>
        </tr>
    @endforelse
    </tbody>
</table>