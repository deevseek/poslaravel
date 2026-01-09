<?php

namespace App\Providers;

use App\Support\SubscriptionFeatureGate;
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
            if (! $user || ! $permissionAbilities->contains($ability)) {
                return null;
            }

            $featureGate = app(SubscriptionFeatureGate::class);
            $allowedPermissions = $featureGate->filterPermissionsForTenant([$ability]);

            if (is_array($allowedPermissions) && ! in_array($ability, $allowedPermissions, true)) {
                return false;
            }

            return $user->hasPermission($ability);
        });
    }
}
