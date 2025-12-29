<?php

namespace App\Http\Controllers;

use App\Tenancy\Models\Subscription;
use App\Tenancy\Models\SubscriptionPlan;
use App\Tenancy\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::orderBy('price')->paginate(10);

        return view('subscription-plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('subscription-plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:subscription_plans,code'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string', 'max:50'],
            'features' => ['nullable', 'string'],
        ]);

        $features = $this->normalizeFeatures($validated['features'] ?? null);

        SubscriptionPlan::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'features' => $features ?: null,
        ]);

        return redirect()->route('subscription-plans.index')
            ->with('success', 'Paket langganan berhasil ditambahkan.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan): View
    {
        $featuresText = implode(PHP_EOL, $subscriptionPlan->features ?? []);

        return view('subscription-plans.edit', compact('subscriptionPlan', 'featuresText'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('subscription_plans', 'code')->ignore($subscriptionPlan->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string', 'max:50'],
            'features' => ['nullable', 'string'],
        ]);

        $features = $this->normalizeFeatures($validated['features'] ?? null);

        $subscriptionPlan->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'features' => $features ?: null,
        ]);

        return redirect()->route('subscription-plans.index')
            ->with('success', 'Paket langganan berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        if (Tenant::where('plan_id', $subscriptionPlan->id)->exists() ||
            Subscription::where('plan_id', $subscriptionPlan->id)->exists()) {
            return redirect()->route('subscription-plans.index')
                ->with('error', 'Paket masih digunakan oleh tenant atau langganan aktif.');
        }

        $subscriptionPlan->delete();

        return redirect()->route('subscription-plans.index')
            ->with('success', 'Paket langganan berhasil dihapus.');
    }

    private function normalizeFeatures(?string $features): array
    {
        if (! $features) {
            return [];
        }

        return collect(preg_split("/\r\n|\r|\n/", $features))
            ->map(fn (string $feature) => trim($feature))
            ->filter()
            ->values()
            ->all();
    }
}
