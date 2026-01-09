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

            // Tidak login → biarkan Laravel
            if (! $user) {
                return null;
            }

            // Ambil tenant AKTIF
            $tenant = app(TenantManager::class)->current();

            // CENTRAL DOMAIN → BEBAS
            if (! $tenant) {
                return null;
            }

            // Ability bukan bagian subscription → biarkan Laravel lanjut
            if (! $permissionAbilities->contains($ability)) {
                return null;
            }

            $allowedPermissions = app(SubscriptionFeatureGate::class)
                ->filterPermissionsForTenant([$ability]) ?? [];

            return in_array($ability, $allowedPermissions, true);
        });
    }
}
