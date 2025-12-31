<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display inventory overview
     */
    public function index()
    {
        $businessId = Auth::user()->business_id;

        // Get inventory items with products
        $inventory = Inventory::with(['product.category'])
            ->where('business_id', $businessId)
            ->orderBy('product_id')
            ->paginate(50);

        // Calculate statistics
        $totalProducts = Inventory::where('business_id', $businessId)->count();

        $lowStockCount = Inventory::where('business_id', $businessId)
            ->whereHas('product', function($q) {
                $q->whereColumn('inventory.quantity', '<=', 'products.reorder_level')
                  ->where('inventory.quantity', '>', 0);
            })
            ->count();

        $outOfStockCount = Inventory::where('business_id', $businessId)
            ->where('quantity', '<=', 0)
            ->count();

        $totalValue = Inventory::where('inventory.business_id', $businessId)
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventory.quantity * products.cost_price) as total')
            ->value('total') ?? 0;

        return view('inventory.index', compact(
            'inventory',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalValue'
        ));
    }

    /**
     * Display inventory overview with stock movements
     */
    public function overview()
    {
        $businessId = Auth::user()->business_id;
        $selectedProduct = request()->get('product');

        // Get all inventory items
        $inventoryItems = Inventory::with('product.category')
            ->where('business_id', $businessId)
            ->orderBy('product_id')
            ->get();

        // Build inventory overview with stock movements
        $inventoryOverview = $inventoryItems->map(function ($inventory) {
            $product = $inventory->product;

            // Get opening stock
            $openingStock = $inventory->quantity;
            
            // Calculate total sales for this product
            $totalSold = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sale_items.product_id', $product->id)
                ->where('sales.business_id', $inventory->business_id)
                ->sum('sale_items.quantity');

            // Calculate opening stock (current + sold)
            $calculatedOpeningStock = $openingStock + $totalSold;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category?->name,
                'opening_stock' => $calculatedOpeningStock,
                'current_stock' => $openingStock,
                'total_sold' => $totalSold,
                'unit_price' => $product->selling_price,
                'unit_cost' => $product->cost_price,
                'value' => $openingStock * $product->selling_price,
                'cost_value' => $openingStock * $product->cost_price
            ];
        });

        // Get recent sales for the selected product (if any)
        $recentSalesTransactions = [];
        if ($selectedProduct) {
            $recentSalesTransactions = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('sale_items.product_id', $selectedProduct)
                ->where('sales.business_id', $businessId)
                ->orderBy('sales.sale_date', 'desc')
                ->limit(50)
                ->select(
                    'sales.id as sale_id',
                    'sales.sale_date',
                    'customers.name as customer_name',
                    'sale_items.quantity',
                    'sale_items.unit_price',
                    'sale_items.total'
                )
                ->get();
        }

        return view('inventory.overview', compact(
            'inventoryOverview',
            'selectedProduct',
            'recentSalesTransactions'
        ));
    }
}