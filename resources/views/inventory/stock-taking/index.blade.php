@extends('layouts.app')

@section('title', 'Stock Taking')

@section('page-title')
    <i class="fas fa-list-check text-indigo-600 mr-2"></i>Stock Taking
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Create New Session Button -->
    <div class="flex justify-end">
        <form method="POST" action="{{ route('stock-taking.create-session') }}" class="inline">
            @csrf
            <button type="submit" class="flex items-center space-x-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus"></i>
                <span>Start New Stock Taking</span>
            </button>
        </form>
    </div>

    <!-- Active Sessions -->
    @php
        $activeSessions = $sessions->where('status', 'active');
    @endphp

    @if($activeSessions->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-yellow-900 mb-4">
            <i class="fas fa-hourglass-half text-yellow-600 mr-2"></i>Active Sessions
        </h3>
        <div class="space-y-2">
            @foreach($activeSessions as $session)
            <div class="flex items-center justify-between bg-white p-4 rounded-lg border border-yellow-200">
                <div>
                    <p class="font-semibold text-gray-900">Session started on {{ $session->session_date->format('M d, Y H:i') }}</p>
                    <p class="text-sm text-gray-600">By: {{ $session->initiator->name }}</p>
                    @if($session->notes)
                    <p class="text-sm text-gray-700 mt-1"><strong>Notes:</strong> {{ $session->notes }}</p>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('stock-taking.session', $session->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-pencil mr-1"></i>Continue
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Past Sessions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-history text-indigo-600 mr-2"></i>Stock Taking History
        </h3>

        @if($sessions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Initiated By</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Products Counted</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Summary</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sessions as $session)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-900">{{ $session->session_date->format('M d, Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($session->status === 'active')
                                    bg-yellow-100 text-yellow-800
                                @elseif($session->status === 'closed')
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $session->initiator->name }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                                {{ $session->adjustments->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $variances = $session->adjustments->where('variance', '!=', 0);
                                $positiveVar = $session->adjustments->where('variance', '>', 0)->sum('variance');
                                $negativeVar = abs($session->adjustments->where('variance', '<', 0)->sum('variance'));
                            @endphp
                            <div class="text-sm space-y-1">
                                @if($variances->count() > 0)
                                    <div class="text-gray-600">
                                        @if($positiveVar > 0)
                                            <span class="text-green-700 font-semibold">↑ +{{ number_format($positiveVar, 2) }} (Overstock)</span>
                                        @endif
                                        @if($negativeVar > 0)
                                            <span class="text-red-700 font-semibold">↓ -{{ number_format($negativeVar, 2) }} (Shortage)</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-500">No variances</span>
                                @endif
                                @if($session->adjustments->where('notes')->count() > 0)
                                    <div class="text-purple-700 text-xs">
                                        📝 {{ $session->adjustments->where('notes')->count() }} item(s) with notes
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('stock-taking.session', $session->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $sessions->links() }}
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-inbox text-4xl mb-2"></i>
            <p class="text-lg">No stock taking sessions yet</p>
            <p class="text-sm">Start a new session to begin counting inventory</p>
        </div>
        @endif
    </div>

</div>
@endsection
