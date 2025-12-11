@extends('layouts.cashier-layout')
@section('title','Record Expense')
@section('page-title','Record Expense')

@section('content')
  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  <div class="card p-6">
    <form method="POST" action="{{ route('cashier.expenses.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Spent By</label>
        <input name="spent_by" type="text" class="mt-1 w-full border rounded p-2" value="{{ old('spent_by', optional(auth()->user())->name) }}" required>
        @error('spent_by')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Purpose</label>
        <input name="purpose" type="text" class="mt-1 w-full border rounded p-2" placeholder="e.g., Transport" value="{{ old('purpose') }}" required>
        @error('purpose')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Amount (UGX)</label>
        <input name="amount" type="number" step="0.01" class="mt-1 w-full border rounded p-2" value="{{ old('amount') }}" required>
        @error('amount')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Date Spent</label>
        <input name="date_spent" type="date" class="mt-1 w-full border rounded p-2" value="{{ old('date_spent', now()->toDateString()) }}" required>
        @error('date_spent')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Notes (optional)</label>
        <textarea name="notes" class="mt-1 w-full border rounded p-2" rows="3">{{ old('notes') }}</textarea>
        @error('notes')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="md:col-span-2 flex items-center justify-end gap-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save Expense</button>
        <a href="{{ route('cashier.expenses.my') }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded">Cancel</a>
      </div>
    </form>
  </div>
@endsection