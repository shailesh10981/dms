<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $this->authorize('location_view');

        $locations = Location::all();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        $this->authorize('location_create');
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $this->authorize('location_create');

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:locations,code',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        Location::create($request->all());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully');
    }

    public function show(Location $location)
    {
        $this->authorize('location_view');

        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $this->authorize('location_edit');
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $this->authorize('location_edit');

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:locations,code,' . $location->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        $location->update($request->all());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully');
    }

    public function destroy(Location $location)
    {
        $this->authorize('location_delete');

        if ($location->departments()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete location with assigned departments');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully');
    }
}
