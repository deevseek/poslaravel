<?php

namespace App\Tenancy\Middleware;

use App\Tenancy\Support\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InitializeTenant
{
    public function __construct(private TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->tenantManager->resolve($request);

        if (! $tenant) {
            if ($this->tenantManager->isCentralHost($request->getHost())) {
                return $next($request);
            }

            abort(404, 'Tenant not found');
        }

        if ($tenant->status === 'suspended') {
            abort(403, 'Tenant suspended');
        }

        $this->tenantManager->switchToTenantConnection($tenant);

        return $next($request);
    }
}
