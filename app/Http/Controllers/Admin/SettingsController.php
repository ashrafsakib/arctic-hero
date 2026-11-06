<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CompanySettings;
use App\Support\PhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(CompanySettings $settings): View
    {
        return view('admin.settings.edit', ['settings' => $settings->all()]);
    }

    public function update(Request $request, CompanySettings $settings): RedirectResponse
    {
        $request->merge([
            'phone' => PhoneNumber::normalize($request->input('phone')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:16', 'regex:/^\+[1-9]\d{7,14}$/'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'company_number' => ['nullable', 'string', 'max:32'],
            'vat_number' => ['nullable', 'string', 'max:32'],
            'service_area' => ['required', 'string', 'max:160'],
            'booking_advance_days' => ['required', 'integer', 'between:1,730'],
        ]);

        $settings->update($data);

        return redirect()->route('admin.settings.edit')->with('success', 'Company and service settings saved.');
    }
}
