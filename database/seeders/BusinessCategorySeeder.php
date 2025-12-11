<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronics and gadgets'],
            ['name' => 'Fashion & Clothing', 'description' => 'Clothing, shoes, and accessories'],
            ['name' => 'Food & Beverage', 'description' => 'Restaurants, cafes, and food stores'],
            ['name' => 'Supermarket & Grocery', 'description' => 'General merchandise and groceries'],
            ['name' => 'Pharmacy & Healthcare', 'description' => 'Medical supplies and pharmaceuticals'],
            ['name' => 'Hardware & Construction', 'description' => 'Building materials and tools'],
            ['name' => 'Automotive', 'description' => 'Auto parts and accessories'],
            ['name' => 'Beauty & Cosmetics', 'description' => 'Beauty products and services'],
            ['name' => 'Stationery & Books', 'description' => 'Office supplies and books'],
            ['name' => 'Home & Furniture', 'description' => 'Home furnishings and appliances'],
            ['name' => 'Agriculture', 'description' => 'Agricultural supplies and produce'],
            ['name' => 'Other', 'description' => 'Other business types'],
        ];

        foreach ($categories as $category) {
            DB::table('business_categories')->insert([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}