<?php

return [
    
    // ===== SALES PERMISSIONS =====
    'sales' => [
        [
            'name' => 'record_sales',
            'display_name' => 'Record Sales (POS)',
            'description' => 'Can use POS system to record sales',
        ],
        [
            'name' => 'view_own_sales',
            'display_name' => 'View Own Sales',
            'description' => 'Can view their own sales only',
        ],
        [
            'name' => 'view_all_sales',
            'display_name' => 'View All Sales',
            'description' => 'Can view sales from all staff members',
        ],
        [
            'name' => 'delete_sales',
            'display_name' => 'Delete Sales',
            'description' => 'Can delete sales records',
        ],
    ],

    // ===== PRODUCT PERMISSIONS =====
    'products' => [
        [
            'name' => 'view_products',
            'display_name' => 'View Products',
            'description' => 'Can view product list',
        ],
        [
            'name' => 'add_products',
            'display_name' => 'Add Products',
            'description' => 'Can add new products',
        ],
        [
            'name' => 'edit_products',
            'display_name' => 'Edit Products',
            'description' => 'Can edit existing products',
        ],
        [
            'name' => 'delete_products',
            'display_name' => 'Delete Products',
            'description' => 'Can delete products',
        ],
    ],

    // ===== INVENTORY PERMISSIONS =====
    'inventory' => [
        [
            'name' => 'view_inventory',
            'display_name' => 'View Inventory',
            'description' => 'Can view inventory levels',
        ],
        [
            'name' => 'manage_inventory',
            'display_name' => 'Manage Inventory',
            'description' => 'Can adjust stock levels',
        ],
    ],

    // ===== CUSTOMER PERMISSIONS =====
    'customers' => [
        [
            'name' => 'view_customers',
            'display_name' => 'View Customers',
            'description' => 'Can view customer list',
        ],
        [
            'name' => 'manage_customers',
            'display_name' => 'Manage Customers',
            'description' => 'Can add, edit, delete customers',
        ],
    ],

    // ===== SUPPLIER PERMISSIONS =====
    'suppliers' => [
        [
            'name' => 'view_suppliers',
            'display_name' => 'View Suppliers',
            'description' => 'Can view supplier information',
        ],
        [
            'name' => 'manage_suppliers',
            'display_name' => 'Manage Suppliers',
            'description' => 'Can add, edit, delete suppliers',
        ],
    ],

    // ===== REPORT PERMISSIONS =====
    'reports' => [
        [
            'name' => 'view_reports',
            'display_name' => 'View Reports',
            'description' => 'Can view sales and inventory reports',
        ],
        [
            'name' => 'generate_reports',
            'display_name' => 'Generate Reports',
            'description' => 'Can generate custom reports',
        ],
    ],

    // ===== STAFF PERMISSIONS =====
    'staff' => [
        [
            'name' => 'view_own_profile',
            'display_name' => 'View Own Profile',
            'description' => 'Can view own performance and profile',
        ],
        [
            'name' => 'view_staff',
            'display_name' => 'View Other Staff',
            'description' => 'Can view other staff members',
        ],
        [
            'name' => 'manage_staff',
            'display_name' => 'Manage Staff',
            'description' => 'Can add, edit, delete staff members',
        ],
    ],

    // ===== SETTINGS PERMISSIONS =====
    'settings' => [
        [
            'name' => 'access_settings',
            'display_name' => 'Access Settings',
            'description' => 'Can access business settings',
        ],
    ],
];