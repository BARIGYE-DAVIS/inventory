@extends('layouts.app')

@section('title', 'Monthly Sales')

@section('page-title')
    <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>Monthly Sales - {{ now()->format('F Y') }}
@endsection

@section('content')
<!-- Stats Card -->
<div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-purple-100 text-sm">Total Sales This Month</p>
            <p class="text-4xl font-bold mt-2">UGX {{ number_format($totalAmount, 0) }}</p>
            <p class="text-purple-100 text-sm mt-1">{{ $totalSales }} transactions • {{ now()->format('F Y') }}</p>
        </div>
        <div class="bg-white bg-opacity-20 rounded-full p-4">
            <i class="fas fa-calendar-alt text-4xl"></i>
        </div>
    </div>
</div>

<!-- Weekly Breakdown Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    @foreach($weeklyBreakdown as $week => $data)
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">{{ $week }}</p>
            <p class="text-2xl font-bold text-purple-600 mt-2">{{ $data['sales'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Sales</p>
            <p class="text-sm font-semibold text-green-600 mt-2">UGX {{ number_format($data['revenue'], 0) }}</p>
        </div>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow-lg p-6">
    
    <!-- Filter Tabs & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-4 md:space-y-0">
        <!-- Filter Tabs -->
        <div class="flex space-x-2 overflow-x-auto">
            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 whitespace-nowrap">
                <i class="fas fa-list mr-1"></i>All Sales
            </a>
            <a href="{{ route('sales.today') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 whitespace-nowrap">
                <i class="fas fa-calendar-day mr-1"></i>Today
            </a>
            <a href="{{ route('sales.weekly') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 whitespace-nowrap">
                <i class="fas fa-calendar-week mr-1"></i>This Week
            </a>
            <a href="{{ route('sales.monthly') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg whitespace-nowrap">
                <i class="fas fa-calendar-alt mr-1"></i>This Month
            </a>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <!-- Print -->
            <button onclick="printReport()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-1"></i>Print
            </button>

            <!-- Export Excel -->
            <a href="{{ route('sales.export.monthly') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-file-excel mr-1"></i>Excel
            </a>

            <!-- Share Dropdown -->
            <div class="relative share-dropdown">
                <button onclick="toggleShareDropdown()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-share-alt mr-1"></i>Share
                </button>
                <div id="shareDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                    <!-- WhatsApp -->
                    <a href="javascript:void(0)" onclick="shareWhatsApp()" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                        <i class="fab fa-whatsapp text-green-600 mr-2"></i>WhatsApp
                    </a>
                    <!-- Email -->
                    <a href="javascript:void(0)" onclick="shareEmail()" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg">
                        <i class="fas fa-envelope text-blue-600 mr-2"></i>Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="overflow-x-auto" id="printableArea">
        <!-- Print Header (Hidden on screen) -->
        <div class="print-only" style="display: none;">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold">{{ auth()->user()->business->name }}</h1>
                <p class="text-gray-600">Monthly Sales Report</p>
                <p class="text-gray-600">{{ now()->format('F Y') }}</p>
                <hr class="my-4">
            </div>
        </div>

        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase no-print">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sales as $sale)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $sale->sale_date->format('M d') }}<br>
                        <span class="text-xs text-gray-400">{{ $sale->sale_date->format('h:i A') }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm font-medium text-indigo-600">
                        {{ $sale->sale_number }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $sale->customer->name ?? 'Walk-in' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $sale->user->name }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $sale->items->count() }} items
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-600">
                        UGX {{ number_format($sale->total, 0) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ ($sale->payment_status ?? 'paid') === 'paid' ? 'bg-green-100 text-green-800' : 
                               (($sale->payment_status ?? 'paid') === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($sale->payment_status ?? 'Paid') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 no-print">
                        <a href="{{ route('sales.show', $sale) }}" 
                           class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-2"></i>
                        <p>No sales this month yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">TOTAL:</td>
                    <td class="px-4 py-3 text-sm font-bold text-green-600">UGX {{ number_format($totalAmount, 0) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Print Footer (Hidden on screen) -->
        <div class="print-only" style="display: none;">
            <hr class="my-4">
            <div class="text-sm text-gray-600">
                <p>Total Sales: {{ $totalSales }} transactions</p>
                <p>Total Amount: UGX {{ number_format($totalAmount, 0) }}</p>
                <p>Period: {{ now()->format('F Y') }}</p>
                <p>Printed on: {{ now()->format('d M Y h:i A') }}</p>
                <p>Printed by: {{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-only {
            display: block !important;
        }
        
        .shadow-lg, .rounded-xl {
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .bg-gray-50, .bg-purple-50, .hover\:bg-gray-50 {
            background-color: white !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle Share Dropdown
    function toggleShareDropdown() {
        const dropdown = document.getElementById('shareDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('shareDropdown');
        const shareButton = event.target.closest('.share-dropdown');
        
        if (!shareButton && dropdown) {
            dropdown.classList.add('hidden');
        }
    });

    // Print Report
    function printReport() {
        window.print();
    }

    // Share via WhatsApp
    function shareWhatsApp() {
        const businessName = "{{ auth()->user()->business->name }}";
        const totalSales = "{{ $totalSales }}";
        const totalAmount = "{{ number_format($totalAmount, 0) }}";
        const month = "{{ now()->format('F Y') }}";
        
        const message = `📊 *Monthly Sales Report* 📊\n\n` +
                       `🏢 ${businessName}\n` +
                       `📅 ${month}\n\n` +
                       `💰 Total Sales: UGX ${totalAmount}\n` +
                       `🛒 Transactions: ${totalSales}\n\n` +
                       `Generated by POS System`;
        
        const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
        
        // Close dropdown
        document.getElementById('shareDropdown').classList.add('hidden');
    }

    // Share via Email
    function shareEmail() {
        const businessName = "{{ auth()->user()->business->name }}";
        const totalSales = "{{ $totalSales }}";
        const totalAmount = "{{ number_format($totalAmount, 0) }}";
        const month = "{{ now()->format('F Y') }}";
        
        const subject = `Monthly Sales Report - ${month}`;
        const body = `Monthly Sales Report\n\n` +
                    `Business: ${businessName}\n` +
                    `Period: ${month}\n\n` +
                    `Total Sales: UGX ${totalAmount}\n` +
                    `Transactions: ${totalSales}\n\n` +
                    `Generated by POS System`;
        
        const mailtoUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.location.href = mailtoUrl;
        
        // Close dropdown
        document.getElementById('shareDropdown').classList.add('hidden');
    }
</script>
@endpush