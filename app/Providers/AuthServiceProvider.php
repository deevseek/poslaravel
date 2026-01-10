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
            if (! $user) {
                return null;
            }

            $hasPermission = $user->hasPermission($ability);

            // === CENTRAL DOMAIN ===
            // User pusat → BEBAS
            if ($user->hasPermission('tenant.manage')) {
                return true;
            }

            // === TENANT ===
            // Ability bukan bagian subscription → cek permission biasa
            if (! $permissionAbilities->contains($ability)) {
                return $hasPermission;
            }

            $allowedPermissions = app(SubscriptionFeatureGate::class)
                ->filterPermissionsForTenant([$ability]);

            if ($allowedPermissions === null) {
                return $hasPermission;
            }

            return $hasPermission && in_array($ability, $allowedPermissions, true);
        });
    }
}
