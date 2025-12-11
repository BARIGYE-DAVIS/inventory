@extends('layouts.app')
@section('title','Today\'s Expenses')
@section('page-title','Today\'s Expenses')

@section('content')
  <!-- GET form: date range for charts only; list initially unfiltered -->
  <div class="card p-6 mb-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="text-xs text-gray-600">Start Date (charts)</label>
        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? optional($range['start'])->toDateString() }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div>
        <label class="text-xs text-gray-600">End Date (charts)</label>
        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? optional($range['end'])->toDateString() }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div class="flex items-end justify-end gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Apply</button>
        <a href="{{ route('expenses.today') }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded">Reset</a>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-6">
      <h3 class="text-lg font-bold">Current</h3>
      <p class="text-sm text-gray-500">
        {{ optional($range['start'])->format('D, M d, Y') }} – {{ optional($range['end'])->format('D, M d, Y') }}
      </p>
      <p class="text-2xl font-extrabold text-rose-600 mt-2">UGX {{ number_format($chart['values'][1] ?? 0, 0) }}</p>
    </div>
    
    <div class="card p-6 md:col-span-1">
      <h3 class="text-lg font-bold">Comparison</h3>
      <canvas id="todayCompare" height="110"></canvas>
    </div>
  </div>

  <!-- List toolbar: client-side search -->
  <div class="flex items-center justify-between mb-2">
    <div class="text-sm text-gray-500">
      Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }}
    </div>
    <div class="w-64">
      <input id="listSearchToday" type="text" placeholder="Search this list..." class="w-full border rounded p-2 text-sm" autocomplete="off">
    </div>
  </div>

  <div class="card p-0 overflow-x-auto">
    <table id="expensesTableToday" class="min-w-full">
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

  <div class="mt-4">
    {{ $expenses->withQueryString()->links() }}
  </div>

  <script>
    // List search
    (function(){
      function attachListSearch(inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (!input || !table) return;
        input.addEventListener('input', function(){
          const q = (input.value || '').toLowerCase().trim();
          const rows = table.querySelectorAll('tbody .expense-row');
          rows.forEach(tr => {
            const text = tr.textContent.toLowerCase();
            tr.style.display = (!q || text.includes(q)) ? '' : 'none';
          });
        });
      }
      attachListSearch('listSearchToday', 'expensesTableToday');
    })();

    // Chart
    if (window.Chart) {
      const labels = @json($chart['labels']);
      const values = @json($chart['values']);
      const fmt = v => (Number(v)||0).toLocaleString();
      new Chart(document.getElementById('todayCompare').getContext('2d'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Total', data: values, backgroundColor: ['rgba(234,179,8,0.85)','rgba(59,130,246,0.85)'] }] },
        options: { plugins: { tooltip: { callbacks: { label: c => `UGX ${fmt(c.parsed.y)}` } }, legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      });
    }
  </script>
@endsection