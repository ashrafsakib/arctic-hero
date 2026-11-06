<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    private const STATUSES = ['visible', 'hidden'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:'.implode(',', self::STATUSES)],
        ]);

        return view('admin.reviews.index', [
            'reviews' => Review::query()
                ->with(['customer', 'booking'])
                ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'admin_response' => ['nullable', 'string', 'max:2000'],
        ]);

        $response = filled($data['admin_response'] ?? null) ? $data['admin_response'] : null;
        $review->update([
            'status' => $data['status'],
            'admin_response' => $response,
            'admin_responded_at' => $response === null ? null : now(),
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review moderation updated.');
    }
}
