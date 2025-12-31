<?php

namespace App\Http\Controllers;

use App\Models\{Product, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display products list
     */
   public function index(Request $request)
{
    $user = Auth::user();
    $userRole = $user->role->name;

    // ✅ Start query WITHOUT forcing is_active = true
    $query = Product::where('business_id', $user->business_id)
        ->with('category');

    // ✅ Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // ✅ Category filter (use category_id, not category)
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // ✅ Status filter
    if ($request->filled('status')) {
        switch ($request->status) {
            case 'active':
                $query->where('is_active', true);
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'low_stock':
                $query->where('is_active', true)
                      ->whereColumn('quantity', '<=', 'reorder_level')
                      ->where('quantity', '>', 0);
                break;
            case 'out_of_stock':
                $query->where('is_active', true)
                      ->where('quantity', '<=', 0);
                break;
        }
    } else {
        // ✅ Default: show only active products if no status filter
        $query->where('is_active', true);
    }

    $products = $query->orderBy('name')->paginate(20);

    // Get categories for filter
    $categories = Category::where('business_id', $user->business_id)
        ->orderBy('name')
        ->get();

    // ✅ AJAX REQUEST - Return JSON with HTML (for cashier table view)
    if ($request->ajax() || $request->has('ajax')) {
        $html = '';
        
        if ($products->count() > 0) {
            foreach ($products as $product) {
                $stockClass = $product->quantity < 10 ? 'text-red-600 font-bold' : 'text-gray-600';
                $categoryHtml = $product->category 
                    ? '<span class="inline-block px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">' . $product->category->name . '</span>'
                    : '<span class="text-gray-400">-</span>';
                
                $html .= '
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">' . $product->name . '</td>
                    <td class="px-4 py-3 text-sm text-gray-600">' . ($product->sku ?? '-') . '</td>
                    <td class="px-4 py-3 text-sm text-gray-600">' . $categoryHtml . '</td>
                    <td class="px-4 py-3 text-sm font-bold text-green-600 text-right">UGX ' . number_format($product->selling_price, 0) . '</td>
                    <td class="px-4 py-3 text-sm text-right ' . $stockClass . '">' . $product->quantity . ' ' . ($product->unit ?? 'pcs') . '</td>
                    <td class="px-4 py-3 text-center">
                        <a href="' . route('products.show', $product->id) . '" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                    </td>
                </tr>';
            }
        } else {
            $html = '
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-search-minus text-3xl mb-2"></i>
                    <p class="text-lg">No products found</p>
                </td>
            </tr>';
        }
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $products->total()
        ]);
    }

    // ✅ LOAD DIFFERENT VIEW BASED ON ROLE
    if ($userRole === 'cashier') {
        return view('cashier.products-index', compact('products', 'categories'));
    }

    return view('products.index', compact('products', 'categories'));
}

    /**
     * Show product details
     */
    public function show(Product $product)
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        if ($product->business_id !== $user->business_id) {
            abort(403);
        }

        $product->load('category');

        // Get recent sales of this product
        $recentSales = $product->saleItems()
            ->with('sale.customer')
            ->latest()
            ->limit(10)
            ->get();

        // ✅ LOAD DIFFERENT VIEW BASED ON ROLE
        if ($userRole === 'cashier') {
            return view('cashier.products-show', compact('product', 'recentSales'));
        }

        return view('products.show', compact('product', 'recentSales'));
    }

    /**
     * Show create form (Admin/Owner/Manager only)
     */
    public function create()
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        // Cashiers cannot create products
        if ($userRole === 'cashier') {
            abort(403, 'Cashiers cannot add products.');
        }

        $categories = Category::where('business_id', $user->business_id)
            ->orderBy('name')
            ->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Store new product
     */
    /**
 * Store new product
 */
public function store(Request $request)
{
    $user = Auth::user();
    $userRole = $user->role->name;

    // Cashiers cannot create products
    if ($userRole === 'cashier') {
        abort(403);
    }

    // ✅ UPDATED VALIDATION RULES
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'sku' => 'nullable|string|max:100',
        'barcode' => 'nullable|string|max:100',
        'unit' => 'required|string',
        
        // ✅ Category handling
        'category_id' => 'nullable|exists:categories,id',
        'new_category_name' => 'required_without:category_id|string|max:255',
        'new_category_description' => 'nullable|string',
        
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'quantity' => 'nullable|numeric|min:0',
        'reorder_level' => 'nullable|integer|min:0',
        
        // ✅ Expiry tracking
        'track_expiry' => 'nullable|boolean',
        'manufacture_date' => 'nullable|date',
        'expiry_date' => 'nullable|date|after:manufacture_date',
        'expiry_alert_days' => 'nullable|integer|min:1|max:365',
        
        'description' => 'nullable|string',
    ]);

    // ✅ HANDLE NEW CATEGORY CREATION
    if ($request->filled('new_category_name') && !$request->filled('category_id')) {
        $category = Category::create([
            'name' => $request->new_category_name,
            'description' => $request->new_category_description,
            'business_id' => $user->business_id,
        ]);
        
        $validated['category_id'] = $category->id;
    }

    // ✅ SET BUSINESS ID
    $validated['business_id'] = $user->business_id;
    $validated['is_active'] = true;

    // ✅ SET DEFAULT QUANTITY IF NOT PROVIDED
    if (!isset($validated['quantity'])) {
        $validated['quantity'] = 0;
    }

    // ✅ REMOVE FIELDS NOT IN PRODUCT TABLE
    unset($validated['new_category_name']);
    unset($validated['new_category_description']);
    unset($validated['track_expiry']);

    // ✅ CREATE PRODUCT
    $product = Product::create($validated);

    return redirect()->route('products.index')
        ->with('success', "Product '{$product->name}' added successfully! ✅");
}

    /**
     * Show edit form
     */
    public function edit(Product $product)
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        if ($product->business_id !== $user->business_id) {
            abort(403);
        }

        // Cashiers cannot edit products
        if ($userRole === 'cashier') {
            abort(403, 'Cashiers cannot edit products.');
        }

        $categories = Category::where('business_id', $user->business_id)
            ->orderBy('name')
            ->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        if ($product->business_id !== $user->business_id) {
            abort(403);
        }

        // Cashiers cannot edit products
        if ($userRole === 'cashier') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Product '{$product->name}' updated successfully! ✅");
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        if ($product->business_id !== $user->business_id) {
            abort(403);
        }

        // Only owner/admin can delete
        if (!in_array($userRole, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to delete products.');
        }

        $product->update(['is_active' => false]);

        return redirect()->route('products.index')
            ->with('success', 'Product deactivated successfully!');
    }

    /**
     * Show expired products
     */
    /**
 * Show expired products
 */
public function expired()
{
    $user = Auth::user();
    $userRole = $user->role->name;

    // Cashiers cannot access this
    if ($userRole === 'cashier') {
        abort(403, 'Cashiers cannot access expired products list.');
    }

    // ✅ USE paginate() INSTEAD OF get()
    $products = Product::where('business_id', $user->business_id)
        ->where('is_active', true)
        ->whereNotNull('expiry_date')
        ->where('expiry_date', '<', now())
        ->with('category')
        ->orderBy('expiry_date')
        ->paginate(20); // ✅ Changed from get() to paginate()

    return view('products.expired', compact('products'));
}

    /**
     * Show expiring soon products
     */
  /**
 * Show expiring soon products
 */
public function expiringSoon()
{
    $user = Auth::user();
    $userRole = $user->role->name;

    // Cashiers cannot access this
    if ($userRole === 'cashier') {
        abort(403, 'Cashiers cannot access expiring products list.');
    }

    // ✅ USE paginate() INSTEAD OF get()
    $products = Product::where('business_id', $user->business_id)
        ->where('is_active', true)
        ->whereNotNull('expiry_date')
        ->whereBetween('expiry_date', [now(), now()->addDays(30)])
        ->with('category')
        ->orderBy('expiry_date')
        ->paginate(20); // ✅ Changed from get() to paginate()

    return view('products.expiring-soon', compact('products'));
}

/**
 * Show import products form
 */
public function showImport()
{
    $categories = Category::where('business_id', Auth::user()->business_id)
        ->orderBy('name')
        ->get();

    return view('products.import', compact('categories'));
}

/**
 * Handle product import from CSV/Excel
 */
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120', // 5MB max
    ], [
        'file.required' => 'Please select a file to import',
        'file.mimes' => 'File must be CSV or Excel format',
        'file.max' => 'File size must not exceed 5MB'
    ]);

    $businessId = Auth::user()->business_id;
    $file = $request->file('file');
    $fileContents = file_get_contents($file->path());

    // Handle both CSV and Excel files
    if (in_array($file->getClientOriginalExtension(), ['xlsx', 'xls'])) {
        $products = $this->parseExcelFile($file);
    } else {
        $products = $this->parseCSVFile($fileContents);
    }

    if (empty($products)) {
        return back()->withErrors(['file' => 'The file is empty or has an invalid format']);
    }

    $imported = 0;
    $errors = [];
    $row = 2; // Start from row 2 (after header)

    foreach ($products as $productData) {
        try {
            // Validate required fields
            if (empty($productData['name'])) {
                $errors[] = "Row $row: Product name is required";
                $row++;
                continue;
            }

            if (empty($productData['sku'])) {
                $errors[] = "Row $row: SKU is required";
                $row++;
                continue;
            }

            // Check if product already exists by SKU
            $existingProduct = Product::where('business_id', $businessId)
                ->where('sku', $productData['sku'])
                ->first();

            if ($existingProduct) {
                $errors[] = "Row $row: Product with SKU '{$productData['sku']}' already exists";
                $row++;
                continue;
            }

            // Get or create category
            $categoryId = null;
            if (!empty($productData['category'])) {
                $category = Category::firstOrCreate(
                    ['business_id' => $businessId, 'name' => $productData['category']],
                    ['description' => '']
                );
                $categoryId = $category->id;
            }

            // Create product
            Product::create([
                'business_id' => $businessId,
                'category_id' => $categoryId,
                'name' => $productData['name'],
                'sku' => $productData['sku'],
                'description' => $productData['description'] ?? '',
                'cost_price' => (float)($productData['cost_price'] ?? 0),
                'selling_price' => (float)($productData['selling_price'] ?? 0),
                'quantity' => (int)($productData['quantity'] ?? 0),
                'unit' => $productData['unit'] ?? 'pcs',
                'barcode' => $productData['barcode'] ?? null,
                'expiry_date' => !empty($productData['expiry_date']) ? date('Y-m-d', strtotime($productData['expiry_date'])) : null,
                'is_active' => true
            ]);

            $imported++;
        } catch (\Exception $e) {
            $errors[] = "Row $row: Error importing product - " . $e->getMessage();
        }

        $row++;
    }

    if ($imported > 0) {
        $message = "Successfully imported $imported product" . ($imported > 1 ? 's' : '');
        if (!empty($errors)) {
            $message .= '. ' . count($errors) . ' row(s) had errors';
        }
    } else {
        $message = 'No products were imported';
    }

    return redirect()->route('products.index')
        ->with('success', $message)
        ->with('import_errors', $errors);
}

/**
 * Parse CSV file and return products array
 */
private function parseCSVFile($fileContents)
{
    $lines = array_filter(array_map('str_getcsv', explode("\n", $fileContents)));
    
    if (empty($lines)) {
        return [];
    }

    $header = array_shift($lines);
    $products = [];

    foreach ($lines as $line) {
        if (count($line) < count($header)) {
            $line = array_pad($line, count($header), '');
        }

        $product = array_combine($header, array_slice($line, 0, count($header)));
        
        // Clean keys (remove spaces and convert to lowercase)
        $product = array_combine(
            array_map(function($key) { return strtolower(trim($key)); }, array_keys($product)),
            $product
        );

        if (!empty($product['name'])) {
            $products[] = $product;
        }
    }

    return $products;
}

/**
 * Parse Excel file and return products array
 */
private function parseExcelFile($file)
{
    try {
        $zip = new \ZipArchive();
        
        if ($zip->open($file->path()) !== true) {
            return [];
        }

        // Read shared strings (text values)
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        $sharedStrings = [];
        
        if ($sharedStringsXml !== false) {
            $doc = new \DOMDocument();
            $doc->loadXML($sharedStringsXml);
            $stringElements = $doc->getElementsByTagName('si');
            
            foreach ($stringElements as $si) {
                $tElements = $si->getElementsByTagName('t');
                $value = '';
                foreach ($tElements as $t) {
                    $value .= $t->nodeValue;
                }
                $sharedStrings[] = $value;
            }
        }

        // Read worksheet
        $worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        
        if ($worksheetXml === false) {
            return [];
        }

        // Parse worksheet
        $doc = new \DOMDocument();
        $doc->loadXML($worksheetXml);
        
        $rows = $doc->getElementsByTagName('row');
        $products = [];
        $headerRow = null;
        $rowIndex = 0;

        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('c');
            $rowData = [];
            $colIndex = 0;

            foreach ($cells as $cell) {
                $value = '';
                $cellType = $cell->getAttribute('t');
                
                // Get the cell value
                $cellValue = $cell->getElementsByTagName('v');
                if ($cellValue->length > 0) {
                    $value = $cellValue->item(0)->nodeValue;
                    
                    // If it's a shared string reference, get the actual string
                    if ($cellType === 's') {
                        $stringIndex = (int)$value;
                        $value = isset($sharedStrings[$stringIndex]) ? $sharedStrings[$stringIndex] : '';
                    }
                }
                
                $rowData[$colIndex] = trim($value);
                $colIndex++;
            }

            if ($rowIndex === 0) {
                // First row is header
                $headerRow = array_map(function($h) { 
                    return strtolower(trim($h)); 
                }, $rowData);
            } else {
                // Skip empty rows
                if (!empty(array_filter($rowData))) {
                    // Combine with header
                    if ($headerRow) {
                        $product = array_combine(
                            $headerRow,
                            array_pad($rowData, count($headerRow), '')
                        );
                        if (!empty($product['name'])) {
                            $products[] = $product;
                        }
                    }
                }
            }

            $rowIndex++;
        }

        return $products;
    } catch (\Exception $e) {
        return [];
    }
}

/**
 * Download import template
 */
public function downloadTemplate()
{
    $file = storage_path('app/templates/product-import-template.csv');
    
    // Create template if it doesn't exist
    if (!file_exists($file)) {
        @mkdir(dirname($file), 0755, true);
        
        $header = ['Name', 'SKU', 'Category', 'Description', 'Cost Price', 'Selling Price', 'Quantity', 'Unit', 'Barcode', 'Expiry Date'];
        $fp = fopen($file, 'w');
        fputcsv($fp, $header);
        
        // Add sample data
        $sample = [
            'Sample Product 1',
            'SKU001',
            'Electronics',
            'Sample description',
            '100.00',
            '150.00',
            '50',
            'pcs',
            '1234567890',
            '2025-12-31'
        ];
        fputcsv($fp, $sample);
        fclose($fp);
    }

    return response()->download($file, 'product-import-template.csv');
}

}