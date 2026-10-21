<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'topic' => ['required', 'in:booking,vehicle,service,other'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'website' => ['prohibited'],
        ]);

        $submission = ContactSubmission::create([
            ...$data,
            'status' => ContactSubmission::NEW,
            'email_status' => 'pending',
        ]);

        try {
            Mail::to(config('company.email'))->send(new ContactMessage($data));
            $submission->update([
                'email_status' => 'sent',
                'email_sent_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $submission->update(['email_status' => 'failed']);
        }

        return redirect()->route('contact.index')->with([
            'contact_sent' => true,
            'contact_mail_failed' => $submission->email_status === 'failed',
        ]);
    }
}