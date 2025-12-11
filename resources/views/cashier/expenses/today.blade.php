@extends('layouts.cashier-layout')
@section('title','Today\'s Expenses')
@section('page-title','Today\'s Expenses')

@section('content')
  <!-- Chart date range (optional) -->
  <div class="card p-6 mb-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="text-xs text-gray-600">Start Date (charts)</label>
        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div>
        <label class="text-xs text-gray-600">End Date (charts)</label>
        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div class="flex items-end justify-end gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Apply</button>
        <a href="{{ route('cashier.expenses.today') }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded">Reset</a>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-6">
      <h3 class="text-lg font-bold">Current</h3>
      <p class="text-sm text-gray-500">
        {{ optional(($chart['range'] ?? [])['start'])->format('D, M d, Y') }} – {{ optional(($chart['range'] ?? [])['end'])->format('D, M d, Y') }}
      </p>
      <p class="text-2xl font-extrabold text-rose-600 mt-2">UGX {{ number_format(($chart['values'][1] ?? 0), 0) }}</p>
    </div>
    <div class="card p-6">
      <h3 class="text-lg font-bold">Previous</h3>
      <p class="text-sm text-gray-500">
        {{ optional(($chart['range'] ?? [])['prevStart'])->format('D, M d, Y') }} – {{ optional(($chart['range'] ?? [])['prevEnd'])->format('D, M d, Y') }}
      </p>
      <p class="text-2xl font-extrabold text-rose-600 mt-2">UGX {{ number_format(($chart['values'][0] ?? 0), 0) }}</p>
    </div>
    <div class="card p-6 md:col-span-1">
      <h3 class="text-lg font-bold">Comparison</h3>
      <canvas id="cashierTodayBar" height="110"></canvas>
    </div>
  </div>

  <!-- List toolbar: client-side search -->
  <div class="flex items-center justify-between mb-2">
    <div class="text-sm text-gray-500">Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }}</div>
    <div class="w-64">
      <input id="listSearchCashierToday" type="text" placeholder="Search this list..." class="w-full border rounded p-2 text-sm" autocomplete="off">
    </div>
  </div>

  <div class="card p-0 overflow-x-auto">
    <table id="expensesTableCashierToday" class="min-w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Purpose</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Spent By</th>
          <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Notes</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($expenses as $e)
          <tr class="hover:bg-gray-50 expense-row">
            <td class="px-4 py-3 text-sm text-gray-700">{{ optional($e->created_at)->format('h:i A') }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ $e->purpose }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ $e->spent_by }}</td>
            <td class="px-4 py-3 text-sm text-right font-semibold text-rose-600">UGX {{ number_format($e->amount, 0) }}</td>
            <td class="px-4 py-3 text-sm text-gray-500">{{ $e->notes }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No expenses found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $expenses->withQueryString()->links() }}</div>

  <script>
    // List search
    (function(){
      const input = document.getElementById('listSearchCashierToday');
      const table = document.getElementById('expensesTableCashierToday');
      if (!input || !table) return;
      input.addEventListener('input', function(){
        const q = (input.value || '').toLowerCase().trim();
        table.querySelectorAll('tbody .expense-row').forEach(tr => {
          tr.style.display = (!q || tr.textContent.toLowerCase().includes(q)) ? '' : 'none';
        });
      });
    })();

    // Chart
    if (window.Chart) {
      const labels = @json(($chart['labels'] ?? ['Previous','Current']));
      const values = @json(($chart['values'] ?? [0,0]));
      const fmt = v => (Number(v)||0).toLocaleString();
      new Chart(document.getElementById('cashierTodayBar').getContext('2d'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Total', data: values, backgroundColor: ['rgba(234,179,8,0.85)','rgba(59,130,246,0.85)'] }] },
        options: { plugins: { tooltip: { callbacks: { label: c => `UGX ${fmt(c.parsed.y)}` } }, legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      });
    }
  </script>
@endsection