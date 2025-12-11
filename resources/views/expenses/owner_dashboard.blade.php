@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Business Expenses</h3>
        <div>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModalOwner">Record Expense</button>
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
                    <th style="width:160px">Recorded By</th>
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
                    <td>{{ optional($e->user)->name }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No expenses found.</td></tr>
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
                    <canvas id="ownerPie" style="max-height:320px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">Expense Trend (last 30 days)</h6>
                    <canvas id="ownerTrend" style="max-height:320px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal in owner_dashboard.blade.php -->
<div class="modal fade" id="addExpenseModalOwner" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="expenseFormOwner"  action="{{ route('expenses.owner_dashboard') }}"  class="modal-content" method="POST" autocomplete="off">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Record Expense</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><input name="spent_by" class="form-control" placeholder="Spent by" required></div>
        <div class="mb-2"><input name="purpose" class="form-control" placeholder="Purpose" required></div>
        <div class="mb-2"><input name="amount" type="number" step="0.01" min="0.01" class="form-control" placeholder="Amount" required></div>
        <div class="mb-2"><input name="date_spent" type="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
        <div class="mb-2"><textarea name="notes" class="form-control" rows="3" placeholder="Notes (optional)"></textarea></div>
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
document.addEventListener('DOMContentLoaded', function() {
    const pieData = json($pieData ?? []);
    const trendData = json($trendData ?? {});

    new Chart(document.getElementById('ownerPie'), {
        type: 'pie',
        data: {
            labels: Object.keys(pieData),
            datasets: [{ data: Object.values(pieData), backgroundColor: ['#1cc88a','#36b9cc','#4e73df','#f6c23e','#e74a3b'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('ownerTrend'), {
        type: 'line',
        data: {
            labels: Object.keys(trendData),
            datasets: [{
                label: 'Amount',
                data: Object.values(trendData),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28,200,138,0.06)',
                fill: true,
                tension: 0.2
            }]
        },
        options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });

    // AJAX submit for owner modal
    const form = document.getElementById('expenseFormOwner');
    if(form){
        form.addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent normal form submit
            const fd = new FormData(form);
            try {
                const resp = await fetch("{{ route('expenses.store') }}", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: fd
                });
                const json = await resp.json();
                if (json.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addExpenseModalOwner'));
                    if(modal) modal.hide();
                    location.reload();
                } else {
                    alert(json.message || "Failed to save expense.");
                }
            } catch (err) {
                alert("Failed to save expense (network or server error).");
            }
        });
    }
});
</script>
@endsection