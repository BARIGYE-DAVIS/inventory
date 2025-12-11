@extends('layouts.app')
@section('title','All Expenses')
@section('page-title','All Expenses')

@section('content')
  <!-- Filters: date range only; buttons right-aligned -->
  <div class="card p-6 mb-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
      <div>
        <label class="text-xs text-gray-600">Start Date</label>
        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div>
        <label class="text-xs text-gray-600">End Date</label>
        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="mt-1 w-full border rounded p-2">
      </div>
      <div class="md:col-span-3 flex items-end justify-end gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Apply</button>
        <a href="{{ url()->current() }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded">Reset</a>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Day-by-day comparison for selected range -->
    <div class="card p-6">
      <h3 class="text-lg font-bold">Day-by-day Comparison</h3>
      <p class="text-xs text-gray-500">
        {{ optional($range['start'])->format('M d, Y') }} – {{ optional($range['end'])->format('M d, Y') }}
        vs
        {{ optional($range['prevStart'])->format('M d, Y') }} – {{ optional($range['prevEnd'])->format('M d, Y') }}
      </p>
      <canvas id="dayCompareChart" height="120"></canvas>
      <ul class="mt-3 text-sm">
        <li><strong>Current total:</strong> UGX {{ number_format($dayCompare['current_total'], 0) }}</li>
        <li><strong>Previous total:</strong> UGX {{ number_format($dayCompare['previous_total'], 0) }}</li>
      </ul>
    </div>

    <!-- Single-year trend -->
    <div class="card p-6">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h3 class="text-lg font-bold">Expenses trend for year {{ $yearTrend['year'] }}</h3>
          <p class="text-xs text-gray-500">Totals by month</p>
        </div>
        <form method="GET" class="flex items-end gap-2">
          <div>
            <label class="text-xs text-gray-600">Year</label>
            <input type="number" name="year" value="{{ $filters['year'] ?? now()->year }}" class="mt-1 w-28 border rounded p-2">
          </div>
          <div class="flex items-center gap-2 mb-[-2px]">
            <button class="px-3 py-2 bg-indigo-600 text-white rounded">Apply</button>
            <a href="{{ route('expenses.index') }}" class="px-3 py-2 bg-gray-100 text-gray-800 rounded">Reset</a>
          </div>
        </form>
      </div>
      <canvas id="yearTrendChart" height="120" class="mt-3"></canvas>
      <ul class="mt-3 text-sm">
        <li><strong>Total {{ $yearTrend['year'] }}:</strong> UGX {{ number_format($yearTrend['total'], 0) }}</li>
      </ul>
    </div>
  </div>

  <!-- List toolbar: client-side search -->
  <div class="flex items-center justify-between mb-2">
    <div class="text-sm text-gray-500">
      Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }}
    </div>
    <div class="w-64">
      <input id="listSearchIndex" type="text" placeholder="Search this list..."
             class="w-full border rounded p-2 text-sm" autocomplete="off">
    </div>
  </div>

  <div class="card p-0 overflow-x-auto">
    <table id="expensesTableIndex" class="min-w-full">
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
        <tr id="noResultsIndex" class="hidden"><td colspan="5" class="px-4 py-8 text-center text-gray-500">No matching rows</td></tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $expenses->withQueryString()->links() }}
  </div>

  <script>
    // Client-side list search (filters current page rows only)
    (function(){
      function attachListSearch(inputId, tableId, noResId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        const noRes = document.getElementById(noResId);
        if (!input || !table) return;
        const rows = () => Array.from(table.querySelectorAll('tbody .expense-row'));
        function filter() {
          const q = (input.value || '').toLowerCase().trim();
          let visible = 0;
          rows().forEach(tr => {
            const text = tr.textContent.toLowerCase();
            if (!q || text.includes(q)) { tr.style.display = ''; visible++; }
            else { tr.style.display = 'none'; }
          });
          if (noRes) noRes.classList.toggle('hidden', visible !== 0 || !q);
        }
        input.addEventListener('input', filter);
      }
      attachListSearch('listSearchIndex', 'expensesTableIndex', 'noResultsIndex');
    })();

    // Charts
    if (window.Chart) {
      const fmt = v => (Number(v)||0).toLocaleString();
      // Day-by-day comparison
      const dayLabels = @json($dayCompare['labels']);
      const curMap = @json($dayCompare['current']);
      const prevMap = @json($dayCompare['previous']);
      const curSeries = dayLabels.map(d => Number(curMap[d] || 0));
      const prevSeries = dayLabels.map(d => Number(prevMap[d] || 0));

      new Chart(document.getElementById('dayCompareChart').getContext('2d'), {
        type: 'line',
        data: {
          labels: dayLabels.map(d => new Date(d).toLocaleDateString()),
          datasets: [
            { label: 'Current', data: curSeries, borderColor: 'rgb(59,130,246)', backgroundColor: 'rgba(59,130,246,0.12)', tension: .35, fill: true, pointRadius: 2 },
            { label: 'Previous', data: prevSeries, borderColor: 'rgb(244,63,94)', backgroundColor: 'rgba(244,63,94,0.12)', tension: .35, fill: true, pointRadius: 2 }
          ]
        },
        options: { interaction: { mode: 'index', intersect: false }, plugins: { tooltip: { callbacks: { label: c => `${c.dataset.label}: UGX ${fmt(c.parsed.y)}` } }, legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
      });

      // Single-year monthly trend
      const yearMonths = @json($yearTrend['labels']);
      const yearSeries = @json($yearTrend['series']);
      const year = @json($yearTrend['year']);

      new Chart(document.getElementById('yearTrendChart').getContext('2d'), {
        type: 'line',
        data: {
          labels: yearMonths,
          datasets: [
            { label: `Expenses ${year}`, data: yearSeries, borderColor: 'rgb(16,185,129)', backgroundColor: 'rgba(16,185,129,0.12)', tension: .35, fill: true, pointRadius: 2 }
          ]
        },
        options: { interaction: { mode: 'index', intersect: false }, plugins: { tooltip: { callbacks: { label: c => `UGX ${fmt(c.parsed.y)}` } }, legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
      });
    }
  </script>
@endsection