<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierCustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        $customers = Customer::where('business_id', $user->business_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(20);

        return view('cashier.customers.index', compact('customers'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        return view('cashier.customers.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'business_id' => $user->business_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'is_active' => true,
        ]);

        return redirect()->route('cashier.customers')
            ->with('success', "Customer '{$customer->name}' added successfully!");
    }
}