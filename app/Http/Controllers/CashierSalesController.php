<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Carbon\Carbon;

class CashierSalesController extends Controller
{
    /**
     * Check if user is cashier
     */
    private function checkCashierRole()
    {
        if (Auth::user()->role->name !== 'cashier') {
            abort(403, 'Only cashiers can access this page.');
        }
    }

    /**
     * Display cashier's sales list
     */
    public function index()
    {
        $this->checkCashierRole();

        $user = Auth::user();
        $businessId = $user->business_id;
        $userId = $user->id;

        // Get cashier's sales with pagination
        $sales = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->with(['customer', 'items.product'])
            ->latest('sale_date')
            ->paginate(20);

        // Calculate statistics
        $totalSales = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->count();

        $totalRevenue = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->sum('total');

        $todaySales = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereDate('sale_date', today())
            ->count();

        $todayRevenue = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereDate('sale_date', today())
            ->sum('total');

        return view('cashier.sales.index', compact(
            'sales',
            'totalSales',
            'totalRevenue',
            'todaySales',
            'todayRevenue'
        ));
    }

    /**
     * Today's sales only
     */
    public function today()
    {
        $this->checkCashierRole();

        $user = Auth::user();
        $businessId = $user->business_id;
        $userId = $user->id;

        // Get today's sales
        $sales = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereDate('sale_date', today())
            ->with(['customer', 'items.product'])
            ->latest('sale_date')
            ->get();

        $totalAmount = $sales->sum('total');
        $totalItems = $sales->sum(function($sale) {
            return $sale->items->sum('quantity');
        });

        // Hourly breakdown
        $hourlyData = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereDate('sale_date', today())
            ->select(
                DB::raw('HOUR(sale_date) as hour'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Payment method breakdown
        $paymentBreakdown = Sale::where('business_id', $businessId)
            ->where('user_id', $userId)
            ->whereDate('sale_date', today())
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('payment_method')
            ->get();

        return view('cashier.sales.today', compact(
            'sales',
            'totalAmount',
            'totalItems',
            'hourlyData',
            'paymentBreakdown'
        ));
    }

    /**
     * Show single sale details
     */
    public function show(Sale $sale)
    {
        $this->checkCashierRole();

        $user = Auth::user();

        // Check if sale belongs to this cashier
        if ($sale->user_id !== $user->id) {
            abort(403, 'You can only view your own sales.');
        }

        // Check if sale belongs to same business
        if ($sale->business_id !== $user->business_id) {
            abort(403, 'Unauthorized access.');
        }

        $sale->load(['customer', 'items.product', 'user']);

        return view('cashier.sales.show', compact('sale'));
    }
}