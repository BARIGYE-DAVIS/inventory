<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'owner',
                'display_name' => 'Business Owner',
                'description' => 'Full access to all features and settings',
                'is_system_role' => true,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Can manage most aspects except critical settings',
                'is_system_role' => true,
            ],
            [
                'name' => 'cashier',
                'display_name' => 'Cashier',
                'description' => 'Can process sales and manage customers',
                'is_system_role' => true,
            ],
            [
                'name' => 'inventory_manager',
                'display_name' => 'Inventory Manager',
                'description' => 'Can manage inventory, products, and stock',
                'is_system_role' => true,
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Accountant',
                'description' => 'Can view reports and financial data',
                'is_system_role' => true,
            ],

             [
                'name' => 'Shop_Attendant',
                'display_name' => 'Accountant',
                'description' => 'Can record sales and manage customer interactions',
                'is_system_role' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'display_name' => $role['display_name'],
                'description' => $role['description'],
                'is_system_role' => $role['is_system_role'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}