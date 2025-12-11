<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        $query = Product::where('business_id', $user->business_id)
            ->where('is_active', true)
            ->with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(20);

        return view('cashier.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        if ($product->business_id !== $user->business_id) {
            abort(403);
        }

        $product->load('category');

        return view('cashier.products.show', compact('product'));
    }
}