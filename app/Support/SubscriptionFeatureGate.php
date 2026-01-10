<?php

namespace App\Support;

use App\Tenancy\Models\Subscription;
use App\Tenancy\Support\TenantManager;

class SubscriptionFeatureGate
{
    public function __construct(private TenantManager $tenantManager)
    {
    }

    public function filterPermissionsForTenant(array $permissions): ?array
    {
        $tenant = $this->tenantManager->current();

        if (! $tenant) {
            return null;
        }

        $subscription = Subscription::query()
            ->with('plan')
            ->where('tenant_id', $tenant->id)
            ->latest('start_date')
            ->first();

        $planFeatures = $subscription?->plan?->features
            ?? $tenant->plan?->features
            ?? [];

        $featurePermissions = collect(config('modules.subscription_feature_permissions', []));

        return collect($planFeatures)
            ->flatMap(fn (string $feature) => $featurePermissions->get($feature, []))
            ->unique()
            ->values()
            ->all();
    }
}
