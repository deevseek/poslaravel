<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\CentralDomain;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RequireFeature;
use App\Http\Middleware\RoleMiddleware;
use App\Tenancy\Middleware\EnsureSubscriptionActive;
use App\Tenancy\Middleware\InitializeTenant;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'central.domain' => CentralDomain::class,
            'feature' => RequireFeature::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'tenant' => InitializeTenant::class,
            'subscription.active' => EnsureSubscriptionActive::class,
        ]);

        $middleware->web(prepend: [
            InitializeTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
