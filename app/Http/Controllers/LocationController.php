<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /**
     * Display all locations for the business
     */
    public function index()
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        // Only owners and managers can manage locations
        if (!in_array($user->role->name, ['owner', 'manager'])) {
            abort(403, 'You do not have permission to manage locations.');
        }

        $locations = Location::where('business_id', $businessId)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('locations.index', compact('locations'));
    }

    /**
     * Show form to create new location
     */
    public function create()
    {
        $user = Auth::user();

        if (!in_array($user->role->name, ['owner', 'manager'])) {
            abort(403, 'You do not have permission to create locations.');
        }

        return view('locations.create');
    }

    /**
     * Store new location
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!in_array($user->role->name, ['owner', 'manager'])) {
            abort(403, 'You do not have permission to create locations.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['business_id'] = $businessId;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_main'] = false; // New locations are not main

        Location::create($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Location created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(Location $location)
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!in_array($user->role->name, ['owner', 'manager'])) {
            abort(403, 'You do not have permission to edit locations.');
        }

        if ($location->business_id !== $businessId) {
            abort(403, 'This location does not belong to your business.');
        }

        return view('locations.edit', compact('location'));
    }

    /**
     * Update location
     */
    public function update(Request $request, Location $location)
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!in_array($user->role->name, ['owner', 'manager'])) {
            abort(403, 'You do not have permission to update locations.');
        }

        if ($location->business_id !== $businessId) {
            abort(403, 'This location does not belong to your business.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Location updated successfully!');
    }

    /**
     * Delete location
     */
    public function destroy(Location $location)
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        if (!in_array($user->role->name, ['owner', 'manager'])) {
            abort(403, 'You do not have permission to delete locations.');
        }

        if ($location->business_id !== $businessId) {
            abort(403, 'This location does not belong to your business.');
        }

        if ($location->is_main) {
            return redirect()->route('locations.index')
                ->with('error', 'Cannot delete the main location.');
        }

        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Location deleted successfully!');
    }
}
