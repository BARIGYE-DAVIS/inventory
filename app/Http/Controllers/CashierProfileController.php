<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class CashierProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        return view('cashier.profile.edit', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('cashier.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role->name !== 'cashier') {
            abort(403);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('cashier.profile')
            ->with('success', 'Password updated successfully!');
    }
}