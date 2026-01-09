<?php

namespace App\Providers;

use App\Support\SubscriptionFeatureGate;
use App\Tenancy\Support\TenantManager;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $permissionAbilities = collect(config('modules.subscription_feature_permissions', []))
            ->flatMap(fn (array $permissions) => $permissions)
            ->filter(fn ($permission) => is_string($permission) && $permission !== '')
            ->unique()
            ->values();

        Gate::before(function ($user, string $ability) use ($permissionAbilities) {
            $tenantManager = app(TenantManager::class);
            $request = app('request');

            if ($tenantManager->isCentralHost($request->getHost())) {
                return null;
            }

            $tenant = $tenantManager->current() ?? $tenantManager->resolve($request);

            if (! $tenant || ! $user) {
                return false;
            }

            if (! $permissionAbilities->contains($ability)) {
            return null; // biarkan Laravel handle policy/ability lain
            }


            $featureGate = app(SubscriptionFeatureGate::class);
            $allowedPermissions = $featureGate->filterPermissionsForTenant([$ability]) ?? [];

            return in_array($ability, $allowedPermissions, true);
        });
    }
}
