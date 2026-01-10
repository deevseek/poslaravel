<?php

namespace App\Support;

use App\Tenancy\Models\Subscription;
use App\Tenancy\Support\TenantManager;

class SubscriptionFeatureService
{
    private ?Subscription $cachedSubscription = null;
    private bool $subscriptionResolved = false;
    private ?array $cachedFeatures = null;
    private bool $featuresResolved = false;

    public function __construct(private TenantManager $tenantManager)
    {
    }

    public function currentSubscription(): ?Subscription
    {
        if ($this->subscriptionResolved) {
            return $this->cachedSubscription;
        }

        $this->subscriptionResolved = true;
        $tenant = $this->tenantManager->current() ?? $this->tenantManager->resolve(request());

        if (! $tenant) {
            return null;
        }

        $this->cachedSubscription = Subscription::query()
            ->with('plan')
            ->where('tenant_id', $tenant->id)
            ->latest('start_date')
            ->first();

        return $this->cachedSubscription;
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->resolveFeatures();

        if ($features === null) {
            return true;
        }

        return in_array($feature, $features, true);
    }

    private function resolveFeatures(): ?array
    {
        if ($this->featuresResolved) {
            return $this->cachedFeatures;
        }

        $this->featuresResolved = true;
        $tenant = $this->tenantManager->current() ?? $this->tenantManager->resolve(request());

        if (! $tenant) {
            return null;
        }

        $subscription = $this->currentSubscription();

        // Gunakan fitur dari subscription terbaru atau fallback ke paket tenant.
        $this->cachedFeatures = $subscription?->plan?->features
            ?? $tenant->plan?->features
            ?? [];

        return $this->cachedFeatures;
    }
}
