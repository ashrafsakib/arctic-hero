<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    private const CATEGORIES = ['airport', 'tourist_attraction', 'transport_hub', 'service_area'];

    public function index(): View
    {
        return view('admin.locations.index', [
            'locations' => Location::query()->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.locations.create', [
            'location' => new Location(['status' => 'active', 'category' => 'tourist_attraction']),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Location::query()->create($this->validatedData($request));

        return redirect()->route('admin.locations.index')->with('success', 'Location and map pin created.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', [
            'location' => $location,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $location->update($this->validatedData($request));

        return redirect()->route('admin.locations.index')->with('success', 'Location and map pin updated.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Location removed.');
    }

    /**
     * @return array{name: string, description: ?string, address: ?string, latitude: ?string, longitude: ?string, category: string, status: string}
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'address' => ['nullable', 'required_if:status,active', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_if:status,active', 'required_with:longitude', 'numeric', 'between:64.5,65.5', 'decimal:0,7'],
            'longitude' => ['nullable', 'required_if:status,active', 'required_with:latitude', 'numeric', 'between:24.8,26.0', 'decimal:0,7'],
            'category' => ['required', 'in:'.implode(',', self::CATEGORIES)],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
