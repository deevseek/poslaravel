<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::if('permission', function ($permissions, bool $requireAll = false) {
            $user = auth()->user();

            if (! $user) {
                return false;
            }

            return $requireAll
                ? $user->hasPermission($permissions)
                : $user->hasAnyPermission($permissions);
        });
    }
}
