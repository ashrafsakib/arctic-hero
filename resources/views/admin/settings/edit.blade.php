<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-800">Company and service settings</h2></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Public contact and booking policy</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Company and service settings</h1><p class="mt-2 text-sm text-slate-600">Saved values are used across public contact details, service notifications, and booking validation.</p></div>
        @if (session('success'))<p class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</p>@endif
        @if ($errors->any())<p class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert">{{ $errors->first() }}</p>@endif
        <section class="border border-slate-200 bg-white p-5 sm:p-6">
            <form method="post" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf @method('PATCH')
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">Company name <input class="mt-1 block min-h-11 w-full border-slate-300" name="name" value="{{ old('name', $settings['name']) }}" maxlength="120" required></label>
                    <label class="text-sm font-medium text-slate-700">Service phone <input class="mt-1 block min-h-11 w-full border-slate-300" name="phone" type="tel" value="{{ old('phone', $settings['phone']) }}" maxlength="30" required></label>
                    <label class="text-sm font-medium text-slate-700">Service email <input class="mt-1 block min-h-11 w-full border-slate-300" name="email" type="email" value="{{ old('email', $settings['email']) }}" maxlength="255" required></label>
                    <label class="text-sm font-medium text-slate-700">Website <span class="font-normal text-slate-500">Optional</span><input class="mt-1 block min-h-11 w-full border-slate-300" name="website" type="url" value="{{ old('website', $settings['website']) }}" maxlength="255"></label>
                    <label class="text-sm font-medium text-slate-700">Company number <input class="mt-1 block min-h-11 w-full border-slate-300" name="company_number" value="{{ old('company_number', $settings['company_number']) }}" maxlength="32"></label>
                    <label class="text-sm font-medium text-slate-700">VAT number <input class="mt-1 block min-h-11 w-full border-slate-300" name="vat_number" value="{{ old('vat_number', $settings['vat_number']) }}" maxlength="32"></label>
                    <label class="text-sm font-medium text-slate-700">Service area <input class="mt-1 block min-h-11 w-full border-slate-300" name="service_area" value="{{ old('service_area', $settings['service_area']) }}" maxlength="160" required></label>
                    <label class="text-sm font-medium text-slate-700">Maximum booking lead time (days)<input class="mt-1 block min-h-11 w-full border-slate-300" name="booking_advance_days" type="number" min="1" max="730" value="{{ old('booking_advance_days', $settings['booking_advance_days']) }}" required><span class="mt-1 block text-xs text-slate-500">Accept bookings from today up to two years ahead.</span></label>
                </div>
                <div class="flex justify-end"><button class="bg-yellow-300 px-5 py-3 text-sm font-bold text-slate-950" type="submit">Save settings</button></div>
            </form>
        </section>
    </div></div>
</x-app-layout>
