<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Carbon\Carbon;

class CashierPerformanceController extends Controller
{
    /**
     * Show cashier performance dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        // Get date range (default: this month)
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Parse dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Base query for cashier's sales
        $salesQuery = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereBetween('sale_date', [$start, $end]);

        // ===== OVERALL STATS =====
        $totalSales = (clone $salesQuery)->count();
        $totalRevenue = (clone $salesQuery)->sum('total');
        $totalItems = (clone $salesQuery)->withSum('items', 'quantity')->get()->sum('items_sum_quantity');
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // ===== TODAY'S PERFORMANCE =====
        $todaySales = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereDate('sale_date', today())
            ->count();

        $todayRevenue = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereDate('sale_date', today())
            ->sum('total');

        // ===== DAILY BREAKDOWN =====
        $dailyBreakdown = (clone $salesQuery)
            ->select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as sales'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // ===== HOURLY PERFORMANCE (TODAY) =====
        $hourlyPerformance = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereDate('sale_date', today())
            ->select(
                DB::raw('HOUR(sale_date) as hour'),
                DB::raw('COUNT(*) as sales'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // ===== TOP SELLING PRODUCTS =====
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.business_id', $businessId)
            ->where('sales.user_id', $user->id)
            ->whereBetween('sales.sale_date', [$start, $end])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // ===== PAYMENT METHOD BREAKDOWN =====
        $paymentMethods = (clone $salesQuery)
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('payment_method')
            ->get();

        // ===== WEEKLY COMPARISON =====
        $thisWeekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        $thisWeekSales = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereBetween('sale_date', [$thisWeekStart, now()])
            ->count();

        $lastWeekSales = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereBetween('sale_date', [$lastWeekStart, $lastWeekEnd])
            ->count();

        $thisWeekRevenue = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereBetween('sale_date', [$thisWeekStart, now()])
            ->sum('total');

        $lastWeekRevenue = Sale::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->whereBetween('sale_date', [$lastWeekStart, $lastWeekEnd])
            ->sum('total');

        // Calculate percentage changes
        $salesChange = $lastWeekSales > 0 
            ? (($thisWeekSales - $lastWeekSales) / $lastWeekSales) * 100 
            : 0;

        $revenueChange = $lastWeekRevenue > 0 
            ? (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100 
            : 0;

        // ===== MONTHLY TARGETS (Optional - can be set by admin) =====
        $monthlyTarget = 100; // Default target sales per month
        $targetProgress = $monthlyTarget > 0 ? ($totalSales / $monthlyTarget) * 100 : 0;

        return view('cashier.performance', compact(
            'totalSales',
            'totalRevenue',
            'totalItems',
            'averageSale',
            'todaySales',
            'todayRevenue',
            'dailyBreakdown',
            'hourlyPerformance',
            'topProducts',
            'paymentMethods',
            'thisWeekSales',
            'lastWeekSales',
            'thisWeekRevenue',
            'lastWeekRevenue',
            'salesChange',
            'revenueChange',
            'monthlyTarget',
            'targetProgress',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export performance report (PDF/Excel)
     */
    public function export(Request $request)
    {
        // Implementation for exporting performance report
        // You can use libraries like Laravel Excel or DomPDF
        
        return response()->json([
            'message' => 'Export functionality coming soon!'
        ]);
    }
}