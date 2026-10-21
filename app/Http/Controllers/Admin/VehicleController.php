<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
                });
            })
            ->when(in_array($request->input('status'), ['active', 'maintenance', 'inactive'], true), fn ($query) => $query->where('status', $request->input('status')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.vehicles.index', [
            'vehicles' => $vehicles,
            'totalVehicles' => Vehicle::count(),
            'activeVehicles' => Vehicle::where('status', 'active')->count(),
            'maintenanceVehicles' => Vehicle::where('status', 'maintenance')->count(),
            'statuses' => ['active', 'maintenance', 'inactive'],
        ]);
    }

    public function create(): View
    {
        return view('admin.vehicles.create', [
            'vehicle' => new Vehicle(),
            'vehicleTypes' => VehicleType::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image) {
            $data['image'] = $image->store('fleet', 'public');
        }

        $vehicle = Vehicle::create($data);

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Vehicle added to the fleet.');
    }

    public function show(Vehicle $vehicle): View
    {
        return view('admin.vehicles.show', [
            'vehicle' => $vehicle->load('vehicleType'),
        ]);
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicles.edit', [
            'vehicle' => $vehicle,
            'vehicleTypes' => VehicleType::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $this->validatedData($request, $vehicle);
        $image = $data['image'] ?? null;
        unset($data['image']);
        $oldImage = $vehicle->image;

        if ($image) {
            $data['image'] = $image->store('fleet', 'public');
        }

        $vehicle->update($data);

        if ($image && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Vehicle details updated.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $image = $vehicle->image;
        $vehicle->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle removed from the fleet.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?Vehicle $vehicle = null): array
    {
        return $request->validate([
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['required', 'string', 'max:255', 'unique:vehicles,registration_number'.($vehicle ? ','.$vehicle->id : '')],
            'color' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'between:1900,'.(now()->year + 1)],
            'status' => ['required', 'in:active,maintenance,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }
}