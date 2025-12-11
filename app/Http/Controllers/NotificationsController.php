<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        // TODO: Replace App\Models\Product with your actual model and field names
        // Assumptions:
        // - expiry_date: DATE/DATETIME
        // - quantity: integer
        // - low stock threshold: configurable (default 10)
        $threshold = (int) config('inventory.low_stock_threshold', 10);

        $now = Carbon::now();

        // Expired: expiry_date < now
        $expiredQuery = \App\Models\Product::query()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', $now)
            ->orderBy('expiry_date', 'desc');

        // Expiring soon: now <= expiry_date <= now + 30 days
        $expiringSoonQuery = \App\Models\Product::query()
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$now, (clone $now)->addDays(30)])
            ->orderBy('expiry_date');

        // Out of stock: quantity <= 0
        $outOfStockQuery = \App\Models\Product::query()
            ->where('quantity', '<=', 0)
            ->orderBy('updated_at', 'desc');

        // Low stock: 1 <= quantity <= threshold
        $lowStockQuery = \App\Models\Product::query()
            ->where('quantity', '>', 0)
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity');

        // Counts
        $expiredCount       = (clone $expiredQuery)->count();
        $expiringSoonCount  = (clone $expiringSoonQuery)->count();
        $outOfStockCount    = (clone $outOfStockQuery)->count();
        $lowStockCount      = (clone $lowStockQuery)->count();

        $totalAlerts = $expiredCount + $expiringSoonCount + $outOfStockCount + $lowStockCount;

        // Previews (limit for badge dropdown)
        $expiredPreview       = (clone $expiredQuery)->limit(5)->get();
        $expiringSoonPreview  = (clone $expiringSoonQuery)->limit(5)->get();
        $outOfStockPreview    = (clone $outOfStockQuery)->limit(5)->get();
        $lowStockPreview      = (clone $lowStockQuery)->limit(5)->get();

        // Full lists for the notifications page (paginate)
        $expired       = $expiredQuery->paginate(10, ['*'], 'expired_page');
        $expiringSoon  = $expiringSoonQuery->paginate(10, ['*'], 'soon_page');
        $outOfStock    = $outOfStockQuery->paginate(10, ['*'], 'out_page');
        $lowStock      = $lowStockQuery->paginate(10, ['*'], 'low_page');

        return view('notifications.index', compact(
            'expiredCount', 'expiringSoonCount', 'outOfStockCount', 'lowStockCount',
            'totalAlerts',
            'expiredPreview', 'expiringSoonPreview', 'outOfStockPreview', 'lowStockPreview',
            'expired', 'expiringSoon', 'outOfStock', 'lowStock', 'threshold'
        ));
    }
}