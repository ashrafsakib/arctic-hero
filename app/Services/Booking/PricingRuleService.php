<?php

namespace App\Services\Booking;

use App\Models\PricingRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PricingRuleService
{
    public function extraChargeFor(Carbon $scheduledAt): float
    {
        return round((float) PricingRule::query()
            ->where('status', 'active')
            ->where(function ($query) use ($scheduledAt): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $scheduledAt);
            })
            ->where(function ($query) use ($scheduledAt): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $scheduledAt);
            })
            ->sum('extra_charge'), 2);
    }

    /**
     * @return Collection<int, array{extra_charge: float, starts_at: ?string, ends_at: ?string}>
     */
    public function activeRules(): Collection
    {
        return PricingRule::query()
            ->where('status', 'active')
            ->get(['extra_charge', 'starts_at', 'ends_at'])
            ->map(fn (PricingRule $rule): array => [
                'extra_charge' => (float) $rule->extra_charge,
                'starts_at' => $rule->starts_at?->format('Y-m-d H:i:s'),
                'ends_at' => $rule->ends_at?->format('Y-m-d H:i:s'),
            ]);
    }
}
