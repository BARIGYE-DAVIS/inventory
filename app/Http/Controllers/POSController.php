<?php

namespace App\Http\Controllers;
use App\Services\MailerService;
use App\Models\{Product, Customer, Sale, SaleItem, Category, Inventory, Location};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Mail, Log};
use App\Mail\SaleReceiptMail; // ✅ ADD THIS

class POSController extends Controller
{
    /**
     * Show POS interface - different view based on role
     */
    public function index()
    {
        $user = Auth::user();
        $businessId = $user->business_id;
        $userRole = $user->role->name;

        // Get user's assigned location or all locations for owner
        $locationId = null;
        if ($userRole !== 'owner' && $user->location_id) {
            $locationId = $user->location_id;
        }

        // Get active inventory items with stock
        $query = Inventory::with('product.category', 'location')
            ->where('business_id', $businessId)
            ->whereHas('product', function($q) {
                $q->where('is_active', true);
            })
            ->where('quantity', '>', 0);

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        // Get inventory grouped by product
        $inventoryItems = $query->get();
        
        // Extract products for compatibility
        $products = $inventoryItems->map(function($item) {
            $product = $item->product;
            $product->inventory_id = $item->id;
            $product->location_id = $item->location_id;
            $product->quantity = $item->quantity; // Use inventory quantity
            return $product;
        });

        // Get categories for filtering
        $categories = Category::where('business_id', $businessId)
            ->withCount(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Get active customers
        $customers = Customer::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get user's location info
        $userLocation = $user->location_id ? Location::find($user->location_id) : null;

        // Load different view based on role
        if ($userRole === 'cashier') {
            return view('cashier.pos', compact('products', 'categories', 'customers', 'userLocation'));
        }

        return view('pos.index', compact('products', 'categories', 'customers', 'userLocation'));
    }

    /**
     * Process sale transaction
     * ✅ FIXED: Now uses Inventory table and respects user location
     */
    public function process(Request $request)
    {
        $user = Auth::user();
        $businessId = $user->business_id;
        $userRole = $user->role->name;

        // Validation rules
        $rules = [
            'customer_option' => ['required', 'in:walk_in,existing,new'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'add_tax' => ['nullable', 'boolean'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        // Add customer validation
        if ($request->customer_option === 'existing') {
            $rules['customer_id'] = ['required', 'exists:customers,id'];
        } elseif ($request->customer_option === 'new') {
            $rules['new_customer_name'] = ['required', 'string', 'max:255'];
            $rules['new_customer_phone'] = ['required', 'string', 'max:20'];
            $rules['new_customer_email'] = ['nullable', 'email', 'max:255'];
            $rules['new_customer_address'] = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $customerId = null;

            // Handle customer
            if ($validated['customer_option'] === 'existing') {
                $customerId = $validated['customer_id'];
            } elseif ($validated['customer_option'] === 'new') {
                $customer = Customer::create([
                    'business_id' => $businessId,
                    'name' => $validated['new_customer_name'],
                    'phone' => $validated['new_customer_phone'],
                    'email' => $validated['new_customer_email'] ?? null,
                    'address' => $validated['new_customer_address'] ?? null,
                    'is_active' => true,
                ]);
                $customerId = $customer->id;
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $discount = $validated['discount'] ?? 0;
            
            // Tax calculation
            $taxAmount = 0;
            if (isset($validated['add_tax']) && $validated['add_tax'] == true) {
                $taxAmount = ($subtotal - $discount) * 0.18;
            }
            
            $total = $subtotal - $discount + $taxAmount;

            // Validate payment
            if ($validated['amount_paid'] < $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount paid (UGX ' . number_format($validated['amount_paid'], 0) . ') is less than total (UGX ' . number_format($total, 0) . ')!',
                ], 400);
            }

            // Generate sale number
            $today = now()->format('Ymd');
            $count = Sale::where('business_id', $businessId)
                ->whereDate('created_at', today())
                ->count() + 1;
            $saleNumber = 'SAL-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Create sale
            $sale = Sale::create([
                'business_id' => $businessId,
                'user_id' => $user->id,
                'customer_id' => $customerId,
                'sale_number' => $saleNumber,
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discount,
                'total' => $total,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create sale items and update inventory
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Determine which location to pull from
                if ($userRole !== 'owner' && $user->location_id) {
                    // Cashier/Staff: Pull from their assigned location
                    $inventory = Inventory::where('business_id', $businessId)
                        ->where('product_id', $item['product_id'])
                        ->where('location_id', $user->location_id)
                        ->first();
                } else {
                    // Owner: Pull from default/main location
                    $location = Location::where('business_id', $businessId)
                        ->where('is_main', true)
                        ->first();
                    $locationId = $location ? $location->id : Inventory::where('business_id', $businessId)
                        ->where('product_id', $item['product_id'])
                        ->value('location_id');
                    
                    $inventory = Inventory::where('business_id', $businessId)
                        ->where('product_id', $item['product_id'])
                        ->where('location_id', $locationId)
                        ->first();
                }

                if (!$inventory || $inventory->quantity < $item['quantity']) {
                    $availableQty = $inventory ? $inventory->quantity : 0;
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$availableQty}");
                }

                // Create sale item
                $saleItem = new SaleItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->product_id = $item['product_id'];
                $saleItem->quantity = $item['quantity'];
                $saleItem->unit_price = $item['price'];
                $saleItem->total = $item['quantity'] * $item['price'];
                $saleItem->save();

                // Update inventory stock
                $inventory->removeStock($item['quantity']);

                // Also update product total quantity for backward compatibility
                $product->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            // ✅ SEND EMAIL RECEIPT (After successful sale)
            $emailMessage = '';
            if ($sale->customer && $sale->customer->email) {
                try {
                    // Load relationships needed for email
                    $sale->load(['business', 'customer', 'items.product', 'user']);
                    
                           MailerService::sendSaleReceipt($sale);
                    
                    $emailMessage = ' | Receipt sent to ' . $sale->customer->email;
                    
                    Log::info('Receipt email sent successfully', [
                        'sale_id' => $sale->id,
                        'customer_email' => $sale->customer->email,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send receipt email', [
                        'sale_id' => $sale->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't fail the sale if email fails
                    $emailMessage = ' | (Email failed to send)';
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!' . $emailMessage,
                'sale_id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'total' => $total,
                'amount_paid' => $validated['amount_paid'],
                'change' => $validated['amount_paid'] - $total,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Sale Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get product details with location-aware stock
     */
    public function getProduct($id)
    {
        $user = Auth::user();
        $businessId = $user->business_id;
        $userRole = $user->role->name;

        $product = Product::where('id', $id)
            ->where('business_id', $businessId)
            ->where('is_active', true)
            ->with('category')
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Get inventory quantity for user's location or all locations
        if ($userRole !== 'owner' && $user->location_id) {
            $stock = Inventory::where('business_id', $businessId)
                ->where('product_id', $id)
                ->where('location_id', $user->location_id)
                ->value('quantity') ?? 0;
        } else {
            $stock = $product->quantity; // Total stock
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->selling_price,
            'stock' => $stock,
            'unit' => $product->unit ?? 'pcs',
            'category' => $product->category->name ?? 'Uncategorized',
            'image' => $product->image_url ?? null,
        ]);
    }

    /**
     * Show receipt
     */
    public function receipt($id)
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        $sale = Sale::where('id', $id)
            ->where('business_id', $user->business_id)
            ->with(['customer', 'user', 'items.product', 'user.business'])
            ->firstOrFail();

        if ($userRole === 'cashier' && $sale->user_id !== $user->id) {
            abort(403, 'You can only view your own sales.');
        }

        if ($userRole === 'cashier') {
            return view('cashier.receipt', compact('sale'));
        }

        return view('pos.receipt', compact('sale'));
    }

    /**
     * Print receipt
     */
    public function printReceipt($id)
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        $sale = Sale::where('id', $id)
            ->where('business_id', $user->business_id)
            ->with(['customer', 'user', 'items.product', 'user.business'])
            ->firstOrFail();

        if ($userRole === 'cashier' && $sale->user_id !== $user->id) {
            abort(403);
        }

        return view('pos.receipt-print', compact('sale'));
    }
}