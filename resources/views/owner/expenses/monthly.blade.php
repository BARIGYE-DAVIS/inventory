@extends('layouts.app')
@section('title','Monthly Expenses')
@section('page-title','Monthly Expenses')

@section('content')
  <!-- GET form: date range + month/year for charts; list initially unfiltered -->
  <div class="card p-6 mb-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
      <div>
        <label class="text-xs text-gray-600">Start Date (charts)</label>
        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? optional($range['start'])->toDateString() }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div>
        <label class="text-xs text-gray-600">End Date (charts)</label>
        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? optional($range['end'])->toDateString() }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div>
        <label class="text-xs text-gray-600">Month</label>
        <input type="number" min="1" max="12" name="month" value="{{ $month }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div>
        <label class="text-xs text-gray-600">Year</label>
        <input type="number" name="year" value="{{ $year }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div class="md:col-span-2 flex items-end justify-end gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Apply</button>
        <a href="{{ route('expenses.monthly', ['month'=>now()->month,'year'=>now()->year]) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded">Reset</a>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card p-6">
      <h3 class="text-lg font-bold">Current Range</h3>
      <p class="text-sm text-gray-500">{{ optional($range['start'])->format('M d, Y') }} – {{ optional($range['end'])->format('M d, Y') }}</p>
      <p class="text-2xl font-extrabold text-rose-600 mt-2">UGX {{ number_format($chart['totals']['cur'] ?? 0, 0) }}</p>
    </div>

    <div class="card p-6">
      <h3 class="text-lg font-bold">Totals Composition</h3>
      <canvas id="monthlyTotalsPie" height="110"></canvas>
    </div>
  </div>

  <div class="card p-6 mb-6">
    <h3 class="text-lg font-bold">Day-by-day Comparison</h3>
    <p class="text-xs text-gray-500">{{ optional($range['start'])->format('F Y') }} vs previous range</p>
    <canvas id="monthlyDaysLine" height="120"></canvas>
  </div>

  <!-- List toolbar: client-side search -->
  <div class="flex items-center justify-between mb-2">
    <div class="text-sm text-gray-500">
      Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }}
    </div>
    <div class="w-64">
      <input id="listSearchMonthly" type="text" placeholder="Search this list..." class="w-full border rounded p-2 text-sm" autocomplete="off">
    </div>
  </div>

  <div class="card p-0 overflow-x-auto">
    <table id="expensesTableMonthly" class="min-w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Purpose</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Spent By</th>
          <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Notes</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse($expenses as $e)
          <tr class="hover:bg-gray-50 expense-row">
            <td class="px-4 py-3 text-sm text-gray-700">{{ optional($e->date_spent)->format('M d, Y') }}</td>
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
      attachListSearch('listSearchMonthly', 'expensesTableMonthly');
    })();

    // Charts
    if (window.Chart) {
      const fmt = v => (Number(v)||0).toLocaleString();
      const labels = @json($chart['labels']);
      const cur = @json($chart['cur']);
      const prev = @json($chart['prev']);
      const totals = @json($chart['totals']);

      new Chart(document.getElementById('monthlyDaysLine').getContext('2d'), {
        type: 'line',
        data: {
          labels,
          datasets: [
            { label: 'Current', data: cur,  borderColor: 'rgb(59,130,246)', backgroundColor: 'rgba(59,130,246,0.12)', tension: .35, fill: true, pointRadius: 2 },
            { label: 'Previous', data: prev, borderColor: 'rgb(234,179,8)', backgroundColor: 'rgba(234,179,8,0.12)', tension: .35, fill: true, pointRadius: 2 },
          ]
        },
        options: { interaction: { mode: 'index', intersect: false }, plugins: { tooltip: { callbacks: { label: c => `${c.dataset.label}: UGX ${fmt(c.parsed.y)}` } }, legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
      });

      new Chart(document.getElementById('monthlyTotalsPie').getContext('2d'), {
        type: 'doughnut',
        data: { labels: ['Current','Previous'], datasets: [{ data: [totals.cur, totals.prev], backgroundColor: ['rgb(59,130,246)','rgb(234,179,8)'] }] },
        options: { plugins: { tooltip: { callbacks: { label: c => `${c.label}: UGX ${fmt(c.parsed)}` } }, legend: { position: 'bottom' } }, cutout: '60%' }
      });
    }
  </script>
@endsection