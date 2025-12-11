<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display inventory overview
     */
    public function index()
    {
        $businessId = Auth::user()->business_id;

        // Get all products with stock info
        $products = Product::where('business_id', $businessId)
            ->with('category')
            ->orderBy('name')
            ->paginate(50);

        // Calculate statistics
        $totalProducts = Product::where('business_id', $businessId)->count();
        
        $lowStockCount = Product::where('business_id', $businessId)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0)
            ->count();
        
        $outOfStockCount = Product::where('business_id', $businessId)
            ->where('quantity', '<=', 0)
            ->count();
        
        $totalValue = Product::where('business_id', $businessId)
            ->selectRaw('SUM(quantity * cost_price) as total')
            ->value('total') ?? 0;

        return view('inventory.index', compact(
            'products',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalValue'
        ));
    }
}