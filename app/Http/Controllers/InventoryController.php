<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display inventory overview by location
     */
    public function index()
    {
        $businessId = Auth::user()->business_id;
        $selectedLocation = request()->get('location');

        // Get all active locations for the business
        $locations = Location::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();

        // Get inventory for all locations
        $query = Inventory::with(['product.category', 'location'])
            ->where('business_id', $businessId);

        if ($selectedLocation) {
            $query->where('location_id', $selectedLocation);
            $currentLocation = Location::findOrFail($selectedLocation);
        } else {
            // Default to main location if it exists, otherwise all locations
            $mainLocation = $locations->where('is_main', true)->first();
            if ($mainLocation) {
                $query->where('location_id', $mainLocation->id);
                $currentLocation = $mainLocation;
                $selectedLocation = $mainLocation->id;
            } else {
                $currentLocation = $locations->first();
                if ($currentLocation) {
                    $query->where('location_id', $currentLocation->id);
                    $selectedLocation = $currentLocation->id;
                }
            }
        }

        // Get inventory items
        $inventory = $query->orderBy('product_id')
            ->paginate(50);

        // Calculate statistics for selected location
        $totalProducts = Inventory::where('business_id', $businessId)
            ->where('location_id', $selectedLocation)
            ->count();

        $lowStockCount = Inventory::where('business_id', $businessId)
            ->where('location_id', $selectedLocation)
            ->whereHas('product', function($q) {
                $q->whereColumn('inventory.quantity', '<=', 'products.reorder_level')
                  ->where('inventory.quantity', '>', 0);
            })
            ->count();

        $outOfStockCount = Inventory::where('business_id', $businessId)
            ->where('location_id', $selectedLocation)
            ->where('quantity', '<=', 0)
            ->count();

        $totalValue = Inventory::where('inventory.business_id', $businessId)
            ->where('location_id', $selectedLocation)
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventory.quantity * products.cost_price) as total')
            ->value('total') ?? 0;

        // Calculate global statistics (all locations)
        $globalTotalProducts = Inventory::where('business_id', $businessId)->count();
        $globalLowStockCount = Inventory::where('business_id', $businessId)
            ->whereHas('product', function($q) {
                $q->whereColumn('inventory.quantity', '<=', 'products.reorder_level')
                  ->where('inventory.quantity', '>', 0);
            })
            ->count();
        $globalOutOfStockCount = Inventory::where('business_id', $businessId)
            ->where('quantity', '<=', 0)
            ->count();
        $globalTotalValue = Inventory::where('inventory.business_id', $businessId)
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventory.quantity * products.cost_price) as total')
            ->value('total') ?? 0;

        return view('inventory.index', compact(
            'inventory',
            'locations',
            'currentLocation',
            'selectedLocation',
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalValue',
            'globalTotalProducts',
            'globalLowStockCount',
            'globalOutOfStockCount',
            'globalTotalValue'
        ));
    }

    /**
     * Display inventory overview with stock movements
     */
    public function overview()
    {
        $businessId = Auth::user()->business_id;
        $selectedLocation = request()->get('location');
        $selectedProduct = request()->get('product');

        // Get all active locations for the business
        $locations = Location::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get();

        // Set default location
        if ($selectedLocation) {
            $currentLocation = Location::findOrFail($selectedLocation);
        } else {
            $mainLocation = $locations->where('is_main', true)->first();
            $currentLocation = $mainLocation ?? $locations->first();
            $selectedLocation = $currentLocation?->id;
        }

        // Get products for this location
        $productQuery = Inventory::with('product.category')
            ->where('business_id', $businessId)
            ->where('location_id', $selectedLocation)
            ->orderBy('product_id');

        if ($selectedProduct) {
            $productQuery->where('product_id', $selectedProduct);
        }

        $inventoryItems = $productQuery->get();

        // Build inventory overview with stock movements
        $inventoryOverview = $inventoryItems->map(function ($inventory) {
            $product = $inventory->product;

            // Get opening stock (first inventory record for this product at this location)
            $openingStock = $inventory->quantity; // Current quantity from inventory table
            
            // Calculate sales from sale_items for this product at this location
            // Include sales with null location_id or matching location_id (for backward compatibility)
            $totalSold = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sale_items.product_id', $product->id)
                ->where('sales.business_id', $inventory->business_id)
                ->where(function($query) use ($inventory) {
                    $query->where('sales.location_id', $inventory->location_id)
                        ->orWhereNull('sales.location_id');
                })
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
                ->where(function($query) use ($selectedLocation) {
                    $query->where('sales.location_id', $selectedLocation)
                        ->orWhereNull('sales.location_id');
                })
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
            'locations',
            'selectedLocation',
            'inventoryOverview',
            'selectedProduct',
            'recentSalesTransactions',
            'currentLocation'
        ));
    }
}