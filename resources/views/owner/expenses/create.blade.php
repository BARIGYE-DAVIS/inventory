@extends('layouts.app')
@section('title','Record Expense')
@section('page-title','Record Expense')
@section('content')
@if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>@endif
<div class="card p-6">
  <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">@csrf
    <div><label>Spent By</label><input name="spent_by" class="w-full border rounded p-2" value="{{ old('spent_by', optional(auth()->user())->name) }}" required></div>
    <div><label>Purpose</label><input name="purpose" class="w-full border rounded p-2" required></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div><label>Amount (UGX)</label><input name="amount" type="number" step="0.01" class="w-full border rounded p-2" required></div>
      <div><label>Date Spent</label><input name="date_spent" type="date" class="w-full border rounded p-2" value="{{ now()->toDateString() }}" required></div>
    </div>
    <div><label>Notes</label><textarea name="notes" class="w-full border rounded p-2" rows="3"></textarea></div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
  </form>
</div>
@endsection