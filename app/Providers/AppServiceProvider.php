<?php

namespace App\Providers;

use App\Support\SubscriptionFeatureGate;
use App\Support\SubscriptionFeatureService;
use App\Tenancy\Support\TenantManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom([
            database_path('migrations/central'),
        ]);

        $isCentralDomain = app(TenantManager::class)->isCentralHost(request()->getHost());

        View::share('isCentralDomain', $isCentralDomain);

        Blade::if('permission', function ($permissions, bool $requireAll = false) {
            $user = auth()->user();

            if (! $user) {
                return false;
            }

            $featureGate = app(SubscriptionFeatureGate::class);
            $permissionList = collect(is_array($permissions) ? $permissions : [$permissions])
                ->flatMap(function (string $permission) {
                    if (str_contains($permission, '|')) {
                        return explode('|', $permission);
                    }

                    return [$permission];
                })
                ->filter()
                ->unique()
                ->values()
                ->all();

            $allowedPermissions = $featureGate->filterPermissionsForTenant($permissionList);

            if (is_array($allowedPermissions)) {
                if ($requireAll && array_diff($permissionList, $allowedPermissions) !== []) {
                    return false;
                }

                if (! $requireAll) {
                    $permissionList = array_values(array_intersect($permissionList, $allowedPermissions));

                    if ($permissionList === []) {
                        return false;
                    }
                }
            }

            return $requireAll
                ? $user->hasPermission($permissionList)
                : $user->hasAnyPermission($permissionList);
        });

        Blade::if('feature', function (string $feature) {
            return app(SubscriptionFeatureService::class)->hasFeature($feature);
        });
    }
}
