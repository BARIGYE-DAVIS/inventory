@extends('layouts.app')

@section('sidebar_links')
<!-- Sidebar links: copy these into your sidebar -->
<ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link" href="{{ route('expenses.index') }}">View Expenses</a></li>
    <li class="nav-item"><a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#addExpenseModalCashier">Record Expense</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('expenses.index') }}#charts">Expense Trends</a></li>
</ul>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">My Expenses</h3>
        <div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModalCashier">Record Expense</button>
        </div>
    </div>

    <form class="mb-3 d-flex gap-2">
        <select name="filter" onchange="this.form.submit()" class="form-select form-select-sm w-auto">
            <option value="" {{ request('filter')=='' ? 'selected':'' }}>All</option>
            <option value="today" {{ request('filter')=='today' ? 'selected':'' }}>Today</option>
            <option value="week" {{ request('filter')=='week' ? 'selected':'' }}>This Week</option>
            <option value="month" {{ request('filter')=='month' ? 'selected':'' }}>This Month</option>
            <option value="year" {{ request('filter')=='year' ? 'selected':'' }}>This Year</option>
        </select>
    </form>

    <div class="table-responsive mb-3">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:120px">Date</th>
                    <th>Spent By</th>
                    <th>Purpose</th>
                    <th class="text-end" style="width:130px">Amount</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            @forelse($expenses as $e)
                <tr>
                    <td>{{ $e->date_spent->format('Y-m-d') }}</td>
                    <td>{{ $e->spent_by }}</td>
                    <td>{{ $e->purpose }}</td>
                    <td class="text-end">{{ number_format($e->amount,2) }}</td>
                    <td>{{ $e->notes }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No expenses found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $expenses->links() }}

    <div id="charts" class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">Expenses Breakdown (by Purpose)</h6>
                    <canvas id="cashierPie" style="max-height:320px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">Expense Trend (last 30 days)</h6>
                    <canvas id="cashierTrend" style="max-height:320px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal (embedded in same file) -->
<div class="modal fade" id="addExpenseModalCashier" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="expenseFormCashier" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Record Expense</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
            <label class="form-label small">Spent By</label>
            <input name="spent_by" class="form-control" placeholder="Person or entity" required>
        </div>
        <div class="mb-2">
            <label class="form-label small">Purpose</label>
            <input name="purpose" class="form-control" placeholder="e.g. Fuel, Lunch" required>
        </div>
        <div class="mb-2">
            <label class="form-label small">Amount</label>
            <input name="amount" type="number" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
        </div>
        <div class="mb-2">
            <label class="form-label small">Date</label>
            <input name="date_spent" type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="mb-2">
            <label class="form-label small">Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Optional"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
(() => {
    const pieData = @json($pieData ?? []);
    const trendData = @json($trendData ?? {});

    // Setup Pie
    new Chart(document.getElementById('cashierPie'), {
        type: 'pie',
        data: {
            labels: Object.keys(pieData),
            datasets: [{ data: Object.values(pieData), backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Setup Trend (line)
    new Chart(document.getElementById('cashierTrend'), {
        type: 'line',
        data: {
            labels: Object.keys(trendData),
            datasets: [{ label: 'Amount', data: Object.values(trendData), borderColor: '#4e73df', backgroundColor: 'rgba(78,115,223,0.05)', fill: true, tension: 0.2 }]
        },
        options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });

    // AJAX submit for cashier modal
    const form = document.getElementById('expenseFormCashier');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        const resp = await fetch('{{ route('expenses.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: fd
        });
        const json = await resp.json();
        if (json.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addExpenseModalCashier'));
            modal.hide();
            location.reload();
        } else {
            alert(json.message || 'Failed to save expense');
        }
    });
})();
</script>
@endsection