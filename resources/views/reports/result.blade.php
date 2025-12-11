@extends('layouts.app')

@section('title', 'Custom Report Results')

@section('page-title')
    <i class="fas fa-file-alt text-indigo-600 mr-2"></i>Custom Report Results
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Report Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 no-print">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ ucfirst($reportType) }} Report</h2>
                <p class="text-gray-600 mt-1">
                    Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                </p>
            </div>
            <div class="flex space-x-2">
                <button onclick="window.print()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="{{ route('reports.custom') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>New Report
                </a>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="overflow-x-auto">
            {!! $reportHtml !!}
        </div>
    </div>

</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
@endsection