<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\{
    DashboardController,
    CashierDashboardController,
    CashierPerformanceController,
    ProfileController,
    StaffController,
    ProductController,
    CategoryController,
    SaleController, 
    PurchaseController,
    InventoryController,
    SupplierController,
    CustomerController,
    ReportController,
    POSController,
    ProfitController,
    SettingsController,
    CashierSalesController,
    CashierCustomerController,
    CashierProductController,
    CashierProfileController
};

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (Login & Register)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // ========================================
    // LOGOUT
    // ========================================
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ========================================
    // MAIN DASHBOARD (Auto-redirects based on role)
    // ========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/annual', [DashboardController::class, 'annual'])->name('dashboard.annual');
    Route::get('/dashboard/annual/export', [DashboardController::class, 'exportAnnual'])->name('dashboard.annual.export');

    // ========================================
    // PROFILE MANAGEMENT (Everyone)
    // ========================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ========================================
    // POS - POINT OF SALE (Everyone)
    // ========================================
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::post('/process', [POSController::class, 'process'])->name('process');
        Route::get('/product/{id}', [POSController::class, 'getProduct'])->name('product');
        Route::get('/receipt/{id}', [POSController::class, 'receipt'])->name('receipt');
        Route::get('/print/{id}', [POSController::class, 'printReceipt'])->name('print');
    });

    // ========================================
    // SALES (Everyone - filtered in controller)
    // ========================================
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/today', [SaleController::class, 'today'])->name('today');
        Route::get('/weekly', [SaleController::class, 'weekly'])->name('weekly');
        Route::get('/monthly', [SaleController::class, 'monthly'])->name('monthly');
        Route::get('/export/today', [SaleController::class, 'exportToday'])->name('export.today');
        Route::get('/export/weekly', [SaleController::class, 'exportWeekly'])->name('export.weekly');
        Route::get('/export/monthly', [SaleController::class, 'exportMonthly'])->name('export.monthly');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
    });

    // ========================================
    // PRODUCTS (Role checked in controller)
    // ========================================
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/expired', [ProductController::class, 'expired'])->name('expired');
        Route::get('/expiring-soon', [ProductController::class, 'expiringSoon'])->name('expiring-soon');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    });

    // ========================================
    // CATEGORIES
    // ========================================
    Route::resource('categories', CategoryController::class);

    // ========================================
    // PROFIT REPORT
    // ========================================
    Route::get('/profit', [ProfitController::class, 'index'])->name('profit.index');

    // ========================================
    // INVENTORY (Role checked in controller)
    // ========================================
    Route::resource('inventory', InventoryController::class);

    // ========================================
    // SUPPLIERS (Role checked in controller)
    // ========================================
    Route::resource('suppliers', SupplierController::class);

    // ========================================
    // CUSTOMERS (Role checked in controller)
    // ========================================
    Route::resource('customers', CustomerController::class);

    // ========================================
    // REPORTS (Role checked in controller)
    // ========================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [ReportController::class, 'products'])->name('products');
        Route::get('/top-selling', [ReportController::class, 'topSelling'])->name('top-selling');
        Route::get('/custom', [ReportController::class, 'custom'])->name('custom');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
    });

    // ========================================
    // STAFF MANAGEMENT (Role checked in controller)
    // ========================================
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
        Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
        Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
        Route::patch('/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])->name('toggle-status');
    });

    // ========================================
    // ✅ BUSINESS SETTINGS (CONSOLIDATED - Admin/Owner only)
    // ========================================
    Route::prefix('settings')->name('settings.')->group(function () {
        
        // Main settings page
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        
        // Business Information
        Route::put('/info', [SettingsController::class, 'updateInfo'])->name('update-info');
        
        // Logo Management
        Route::post('/logo', [SettingsController::class, 'updateLogo'])->name('update-logo');
        Route::delete('/logo', [SettingsController::class, 'removeLogo'])->name('remove-logo');
        
        // Email Settings
        Route::put('/email', [SettingsController::class, 'updateEmail'])->name('update-email');
        Route::post('/email/test', [SettingsController::class, 'testEmail'])->name('test-email');
        Route::delete('/email', [SettingsController::class, 'removeEmail'])->name('remove-email');
        
        // Tax Settings
        Route::put('/tax', [SettingsController::class, 'updateTax'])->name('update-tax');
        
        // Toggle Business Status
        Route::post('/toggle-status', [SettingsController::class, 'toggleStatus'])->name('toggle-status');
    });

    // ========================================
    // PURCHASES (Role checked in controller)
    // ========================================
    Route::resource('purchases', PurchaseController::class);

    // ========================================
    // CASHIER ROUTES (Isolated System)
    // ========================================
    Route::prefix('cashier')->name('cashier.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
        
        // Sales (Cashier-specific views)
        Route::get('/sales', [CashierSalesController::class, 'index'])->name('sales');
        Route::get('/sales/today', [CashierSalesController::class, 'today'])->name('sales.today');
        Route::get('/sales/{sale}', [CashierSalesController::class, 'show'])->name('sales.show');
        
        // Performance (Charts & Reports)
        Route::get('/performance', [CashierPerformanceController::class, 'index'])->name('performance');
        Route::get('/performance/daily', [CashierPerformanceController::class, 'daily'])->name('performance.daily');
        Route::get('/performance/weekly', [CashierPerformanceController::class, 'weekly'])->name('performance.weekly');
        Route::get('/performance/monthly', [CashierPerformanceController::class, 'monthly'])->name('performance.monthly');
        
        // Customers (View & Add only)
        Route::get('/customers', [CashierCustomerController::class, 'index'])->name('customers');
        Route::get('/customers/create', [CashierCustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CashierCustomerController::class, 'store'])->name('customers.store');
        
        // Product Search (Read-only)
        Route::get('/products', [CashierProductController::class, 'index'])->name('products');
        Route::get('/products/{product}', [CashierProductController::class, 'show'])->name('products.show');
        
        // Profile
        Route::get('/profile', [CashierProfileController::class, 'edit'])->name('profile');
        Route::patch('/profile', [CashierProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [CashierProfileController::class, 'updatePassword'])->name('profile.password');
    });
});

use App\Http\Controllers\ExpenseController;

Route::middleware(['auth'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
});

// ========================================
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('index'); // List all invoices (optional)
    Route::get('/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('show'); // Show/print invoice
    Route::get('/{invoice}/print', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('print'); // Optional: print-friendly route
    Route::post('/{invoice}/mark-paid', [\App\Http\Controllers\InvoiceController::class, 'markPaid'])->name('markPaid'); // Mark invoice as paid (optional)
   // Route::post('/invoices/pos', [\App\Http\Controllers\InvoiceController::class, 'posInvoice'])->name('invoices.pos');
});
Route::post('/invoices/pos', [\App\Http\Controllers\InvoiceController::class, 'posInvoice'])->name('invoices.pos');