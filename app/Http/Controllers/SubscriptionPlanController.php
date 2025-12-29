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
        $featureOptions = config('modules.subscription_features', []);

        return view('subscription-plans.create', compact('featureOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $featureOptions = config('modules.subscription_features', []);
        $featureValues = collect($featureOptions)->pluck('value')->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:subscription_plans,code'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string', 'max:50'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', Rule::in($featureValues)],
        ]);

        $features = $this->normalizeFeatures($validated['features'] ?? [], $featureOptions);

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
        $featureOptions = config('modules.subscription_features', []);
        $selectedFeatures = $this->resolveSelectedFeatures($subscriptionPlan->features ?? [], $featureOptions);

        return view('subscription-plans.edit', compact('subscriptionPlan', 'featureOptions', 'selectedFeatures'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $featureOptions = config('modules.subscription_features', []);
        $featureValues = collect($featureOptions)->pluck('value')->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('subscription_plans', 'code')->ignore($subscriptionPlan->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'string', 'max:50'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', Rule::in($featureValues)],
        ]);

        $features = $this->normalizeFeatures($validated['features'] ?? [], $featureOptions);

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

    private function normalizeFeatures(array $features, array $availableFeatures): array
    {
        $featureMap = collect($availableFeatures)
            ->mapWithKeys(fn (array $feature) => [$feature['value'] => $feature['label']]);

        return collect($features)
            ->map(fn (string $feature) => $featureMap->get($feature))
            ->filter()
            ->values()
            ->all();
    }

    private function resolveSelectedFeatures(array $storedFeatures, array $availableFeatures): array
    {
        $featureMap = collect($availableFeatures)
            ->mapWithKeys(fn (array $feature) => [$feature['label'] => $feature['value']]);

        return collect($storedFeatures)
            ->map(fn (string $feature) => $featureMap->get($feature))
            ->filter()
            ->values()
            ->all();
    }
}
