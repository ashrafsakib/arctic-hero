<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PricingRuleController extends Controller
{
    public function index(): View
    {
        return view('admin.pricing-rules.index', [
            'pricingRules' => PricingRule::query()->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.pricing-rules.create', ['pricingRule' => new PricingRule]);
    }

    public function store(Request $request): RedirectResponse
    {
        PricingRule::query()->create($this->validatedData($request));

        return redirect()->route('admin.pricing-rules.index')->with('success', 'Pricing rule created.');
    }

    public function edit(PricingRule $pricingRule): View
    {
        return view('admin.pricing-rules.edit', compact('pricingRule'));
    }

    public function update(Request $request, PricingRule $pricingRule): RedirectResponse
    {
        $pricingRule->update($this->validatedData($request));

        return redirect()->route('admin.pricing-rules.index')->with('success', 'Pricing rule updated.');
    }

    public function destroy(PricingRule $pricingRule): RedirectResponse
    {
        $pricingRule->delete();

        return redirect()->route('admin.pricing-rules.index')->with('success', 'Pricing rule deleted.');
    }

    /**
     * @return array{name: string, description: ?string, extra_charge: string, starts_at: ?string, ends_at: ?string, status: string}
     */
    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'extra_charge' => ['required', 'numeric', 'between:0,1000000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if (! empty($data['starts_at']) && ! empty($data['ends_at']) && $data['ends_at'] <= $data['starts_at']) {
            throw ValidationException::withMessages([
                'ends_at' => 'The end date must be after the start date.',
            ]);
        }

        return $data;
    }
}
