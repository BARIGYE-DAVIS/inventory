<?php

namespace App\Http\Controllers;

use App\Models\{Sale, Product, Customer, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with role-based data
     */
    public function index()
    {
        $user = Auth::user();
        $businessId = $user->business_id;
        $userRole = $user->role->name;

        // ✅ ROLE-BASED DASHBOARD DATA

        // Base query for sales (filtered by role)
        $salesQuery = Sale::where('business_id', $businessId);
        
        // ✅ CASHIER/STAFF SEE ONLY THEIR OWN DATA
        if (in_array($userRole, ['cashier', 'staff'])) {
            $salesQuery->where('user_id', $user->id);
        }

        // Today's statistics
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todayStats = (clone $salesQuery)
            ->whereBetween('sale_date', [$todayStart, $todayEnd])
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue'),
                DB::raw('COALESCE(AVG(total), 0) as avg_sale')
            )
            ->first();

        // This week's statistics
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $weekStats = (clone $salesQuery)
            ->whereBetween('sale_date', [$weekStart, $weekEnd])
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue')
            )
            ->first();

        // This month's statistics
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthStats = (clone $salesQuery)
            ->whereBetween('sale_date', [$monthStart, $monthEnd])
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue')
            )
            ->first();

        // All-time statistics
        $allTimeStats = (clone $salesQuery)
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue')
            )
            ->first();

        // Recent sales (filtered by role)
        $recentSales = (clone $salesQuery)
            ->with(['customer', 'user', 'items.product'])
            ->latest('sale_date')
            ->limit(10)
            ->get();

        // Sales trend (last 7 days)
        $salesTrend = (clone $salesQuery)
            ->whereBetween('sale_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Fill missing dates with zeros
        $salesTrendData = $this->fillMissingDates($salesTrend);

        // ✅ ADMIN/MANAGER/OWNER SEE ADDITIONAL DATA
        $lowStockProducts = null;
        $topSellingProducts = null;
        $totalCustomers = null;
        $activeStaff = null;

        if (in_array($userRole, ['admin', 'manager', 'owner'])) {
            // Low stock products
            $lowStockProducts = Product::where('business_id', $businessId)
                ->whereColumn('quantity', '<=', 'reorder_level')
                ->where('quantity', '>', 0)
                ->orderBy('quantity', 'asc')
                ->limit(5)
                ->get();

            // Top selling products (this month)
            $topSellingProducts = Product::where('products.business_id', $businessId)
                ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                ->join('sales', function($join) use ($monthStart, $monthEnd) {
                    $join->on('sale_items.sale_id', '=', 'sales.id')
                         
