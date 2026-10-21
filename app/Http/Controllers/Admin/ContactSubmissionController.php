<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:new,read,closed'],
            'topic' => ['nullable', 'in:booking,vehicle,service,other'],
        ]);

        $submissions = ContactSubmission::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['topic'] ?? null, fn ($query, string $topic) => $query->where('topic', $topic))
            ->orderByRaw("case status when 'new' then 0 when 'read' then 1 else 2 end")
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.contact-submissions.index', [
            'submissions' => $submissions,
            'newCount' => ContactSubmission::where('status', ContactSubmission::NEW)->count(),
            'readCount' => ContactSubmission::where('status', ContactSubmission::READ)->count(),
            'closedCount' => ContactSubmission::where('status', ContactSubmission::CLOSED)->count(),
            'statuses' => [ContactSubmission::NEW, ContactSubmission::READ, ContactSubmission::CLOSED],
            'topics' => ['booking', 'vehicle', 'service', 'other'],
        ]);
    }

    public function show(ContactSubmission $contactSubmission): View
    {
        if ($contactSubmission->status === ContactSubmission::NEW) {
            $contactSubmission->update([
                'status' => ContactSubmission::READ,
                'read_at' => now(),
            ]);
        }

        return view('admin.contact-submissions.show', [
            'submission' => $contactSubmission->refresh(),
        ]);
    }

    public function update(Request $request, ContactSubmission $contactSubmission): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,closed'],
        ]);

        $contactSubmission->update([
            'status' => $data['status'],
            'read_at' => $data['status'] === ContactSubmission::NEW
                ? null
                : ($contactSubmission->read_at ?? now()),
        ]);

        return redirect()->route('admin.contact-submissions.show', $contactSubmission)
            ->with('success', 'Contact submission status updated.');
    }

    public function destroy(ContactSubmission $contactSubmission): RedirectResponse
    {
        $contactSubmission->delete();

        return redirect()->route('admin.contact-submissions.index')
            ->with('success', 'Contact submission deleted.');
    }
}