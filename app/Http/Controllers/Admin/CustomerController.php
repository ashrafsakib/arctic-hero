<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $customers = User::query()
            ->where('role', 'customer')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->withCount('bookings')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'customerCount' => User::where('role', 'customer')->count(),
            'activeCount' => User::where('role', 'customer')->where('status', 'active')->count(),
            'inactiveCount' => User::where('role', 'customer')->where('status', 'inactive')->count(),
        ]);
    }

    public function show(User $customer): View
    {
        abort_unless($customer->role === 'customer', 404);
        $customer->loadCount('bookings');

        return view('admin.customers.show', [
            'customer' => $customer,
            'bookings' => $customer->bookings()
                ->with('vehicleType')
                ->orderByDesc('booking_date')
                ->orderByDesc('booking_time')
                ->paginate(10),
        ]);
    }

    public function updateStatus(Request $request, User $customer): RedirectResponse
    {
        abort_unless($customer->role === 'customer', 404);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $customer->update(['status' => $data['status']]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer account status updated.');
    }
}
