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
}