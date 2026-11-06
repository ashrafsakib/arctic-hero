<?php

namespace App\Services;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanySettings
{
    private ?array $cachedSettings = null;

    /**
     * @return array{name: string, phone: string, email: string, website: ?string, company_number: string, vat_number: string, service_area: string, booking_advance_days: int}
     */
    public function all(): array
    {
        if ($this->cachedSettings !== null) {
            return $this->cachedSettings;
        }

        $defaults = [
            ...config('company'),
            'service_area' => config('company.service_area', 'Oulu and nearby areas'),
            'booking_advance_days' => (int) config('company.booking_advance_days', 365),
        ];

        if (! Schema::hasTable('company_settings')) {
            return $this->cachedSettings = $defaults;
        }

        $stored = CompanySetting::query()->pluck('value', 'key')->all();
        $settings = array_replace($defaults, $stored);
        $settings['booking_advance_days'] = (int) $settings['booking_advance_days'];

        return $this->cachedSettings = $settings;
    }

    public function bookingAdvanceDays(): int
    {
        return $this->all()['booking_advance_days'];
    }

    /**
     * @param  array<string, string|int|null>  $values
     */
    public function update(array $values): void
    {
        DB::transaction(function () use ($values): void {
            foreach ($values as $key => $value) {
                CompanySetting::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => (string) $value],
                );
            }
        });

        $this->cachedSettings = null;
    }
}
