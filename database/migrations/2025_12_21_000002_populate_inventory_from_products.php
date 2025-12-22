<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Location;
use App\Models\Inventory;

return new class extends Migration
{
    public function up(): void
    {
        // For each business and product, create inventory records
        $products = Product::all();
        
        foreach ($products as $product) {
            // Get or create main location for business
            $location = Location::where('business_id', $product->business_id)
                ->where('is_main', true)
                ->first();
            
            // If no main location exists, create one
            if (!$location) {
                $location = Location::create([
                    'business_id' => $product->business_id,
                    'name' => 'Main Warehouse',
                    'is_main' => true,
                    'is_active' => true,
                ]);
            }
            
            // Create inventory record if not exists
            Inventory::firstOrCreate(
                [
                    'business_id' => $product->business_id,
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                ],
                [
                    'quantity' => $product->quantity,
                ]
            );
        }
    }

    public function down(): void
    {
        // Don't delete inventory data on rollback, just leave it
    }
};
