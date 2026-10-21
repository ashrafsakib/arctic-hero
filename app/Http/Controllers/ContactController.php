<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        Mail::to(config('company.email'))->send(new ContactMessage($data));

        return redirect()->route('contact.index')->with('contact_sent', true);
    }
}