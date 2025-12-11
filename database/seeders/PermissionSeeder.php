<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'module' => 'dashboard'],
            ['name' => 'view_products', 'display_name' => 'View Products', 'module' => 'products'],
            ['name' => 'create_products', 'display_name' => 'Create Products', 'module' => 'products'],
            ['name' => 'edit_products', 'display_name' => 'Edit Products', 'module' => 'products'],
            ['name' => 'delete_products', 'display_name' => 'Delete Products', 'module' => 'products'],
            ['name' => 'view_sales', 'display_name' => 'View Sales', 'module' => 'sales'],
            ['name' => 'create_sales', 'display_name' => 'Create Sales', 'module' => 'sales'],
            ['name' => 'edit_sales', 'display_name' => 'Edit Sales', 'module' => 'sales'],
            ['name' => 'delete_sales', 'display_name' => 'Delete Sales', 'module' => 'sales'],
            ['name' => 'view_purchases', 'display_name' => 'View Purchases', 'module' => 'purchases'],
            ['name' => 'create_purchases', 'display_name' => 'Create Purchases', 'module' => 'purchases'],
            ['name' => 'edit_purchases', 'display_name' => 'Edit Purchases', 'module' => 'purchases'],
            ['name' => 'delete_purchases', 'display_name' => 'Delete Purchases', 'module' => 'purchases'],
            ['name' => 'view_customers', 'display_name' => 'View Customers', 'module' => 'customers'],
            ['name' => 'create_customers', 'display_name' => 'Create Customers', 'module' => 'customers'],
            ['name' => 'edit_customers', 'display_name' => 'Edit Customers', 'module' => 'customers'],
            ['name' => 'delete_customers', 'display_name' => 'Delete Customers', 'module' => 'customers'],
            ['name' => 'view_suppliers', 'display_name' => 'View Suppliers', 'module' => 'suppliers'],
            ['name' => 'create_suppliers', 'display_name' => 'Create Suppliers', 'module' => 'suppliers'],
            ['name' => 'edit_suppliers', 'display_name' => 'Edit Suppliers', 'module' => 'suppliers'],
            ['name' => 'delete_suppliers', 'display_name' => 'Delete Suppliers', 'module' => 'suppliers'],
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'module' => 'reports'],
            ['name' => 'export_reports', 'display_name' => 'Export Reports', 'module' => 'reports'],
            ['name' => 'view_staff', 'display_name' => 'View Staff', 'module' => 'staff'],
            ['name' => 'create_staff', 'display_name' => 'Create Staff', 'module' => 'staff'],
            ['name' => 'edit_staff', 'display_name' => 'Edit Staff', 'module' => 'staff'],
            ['name' => 'delete_staff', 'display_name' => 'Delete Staff', 'module' => 'staff'],
            ['name' => 'view_settings', 'display_name' => 'View Settings', 'module' => 'settings'],
            ['name' => 'edit_settings', 'display_name' => 'Edit Settings', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
                'module' => $permission['module'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign all permissions to owner role
        $ownerRole = DB::table('roles')->where('name', 'owner')->first();
        $allPermissions = DB::table('permissions')->get();

        foreach ($allPermissions as $permission) {
            DB::table('role_permission')->insert([
                'role_id' => $ownerRole->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}