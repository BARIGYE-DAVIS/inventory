<?php

namespace App\Http\Controllers;

use App\Models\{Sale, Product, Customer, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class CashierDashboardController extends Controller
{
    /**
     * Cashier Dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is cashier
        if ($user->role->name !== 'cashier') {
            abort(403, 'Only cashiers can access this dashboard.');
        }

        $businessId = $user->business_id;
        $userId = $user->id;

        // TODAY'S PERFORMANCE
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $todayStats = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereBetween('sale_date', [$todayStart, $todayEnd])
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue'),
                DB::raw('COALESCE(AVG(total), 0) as avg_sale')
            )
            ->first();

        $mySalesToday = $todayStats->sales_count ?? 0;
        $myRevenueToday = $todayStats->revenue ?? 0;
        $myAvgSaleToday = $todayStats->avg_sale ?? 0;

        // WEEK PERFORMANCE
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $weekStats = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereBetween('sale_date', [$weekStart, $weekEnd])
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue')
            )
            ->first();

        $mySalesWeek = $weekStats->sales_count ?? 0;
        $myRevenueWeek = $weekStats->revenue ?? 0;

        // MONTH PERFORMANCE
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthStats = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereBetween('sale_date', [$monthStart, $monthEnd])
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue')
            )
            ->first();

        $mySalesMonth = $monthStats->sales_count ?? 0;
        $myRevenueMonth = $monthStats->revenue ?? 0;

        // ALL-TIME PERFORMANCE
        $allTimeStats = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->select(
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue')
            )
            ->first();

        $myTotalSales = $allTimeStats->sales_count ?? 0;
        $myTotalRevenue = $allTimeStats->revenue ?? 0;

        // RECENT SALES
        $myRecentSales = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->with(['customer', 'items.product'])
            ->latest('sale_date')
            ->limit(10)
            ->get();

        // TOP PRODUCTS SOLD TODAY
        $myTopProducts = Product::where('products.business_id', $businessId)
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', function($join) use ($userId, $todayStart, $todayEnd) {
                $join->on('sale_items.sale_id', '=', 'sales.id')
                     ->where('sales.user_id', $userId)
                     ->whereBetween('sales.sale_date', [$todayStart, $todayEnd]);
            })
            ->select(
                'products.id',
                'products.name',
                'products.image',
                'products.selling_price',
                DB::raw('SUM(sale_items.quantity) as times_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.image', 'products.selling_price')
            ->orderByDesc('times_sold')
            ->limit(10)
            ->get();

        // CASHIER RANKINGS
        $cashierRankings = User::where('users.business_id', $businessId)
            ->whereHas('role', function($query) {
                $query->where('name', 'cashier');
            })
            ->leftJoin('sales', function($join) use ($monthStart, $monthEnd) {
                $join->on('users.id', '=', 'sales.user_id')
                     ->whereBetween('sales.sale_date', [$monthStart, $monthEnd]);
            })
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(sales.id) as sales_count'),
                DB::raw('COALESCE(SUM(sales.total), 0) as revenue')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->get();

        $myRank = $cashierRankings->search(function($item) use ($userId) {
            return $item->id === $userId;
        });

        $myPosition = $myRank !== false ? $myRank + 1 : null;
        $totalCashiers = $cashierRankings->count();

        return view('cashier.dashboard', [
            'mySalesToday' => $mySalesToday,
            'myRevenueToday' => $myRevenueToday,
            'myAvgSaleToday' => $myAvgSaleToday,
            'mySalesWeek' => $mySalesWeek,
            'myRevenueWeek' => $myRevenueWeek,
            'mySalesMonth' => $mySalesMonth,
            'myRevenueMonth' => $myRevenueMonth,
            'myTotalSales' => $myTotalSales,
            'myTotalRevenue' => $myTotalRevenue,
            'myRecentSales' => $myRecentSales,
            'myTopProducts' => $myTopProducts,
            'cashierRankings' => $cashierRankings->take(10),
            'myPosition' => $myPosition,
            'totalCashiers' => $totalCashiers,
        ]);
    }
}