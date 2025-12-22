<?php

namespace App\Tenancy\Middleware;

use App\Tenancy\Support\TenantManager;
use Closure;
use Illuminate\Http\Request;

class EnsureSubscriptionActive
{
    public function __construct(private TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->tenantManager->current() ?? $this->tenantManager->resolve($request);

        if (! $tenant) {
            return $next($request);
        }

        if (! $this->tenantManager->isSubscriptionActive()) {
            abort(402, 'Subscription expired. Please renew to continue.');
        }

        return $next($request);
    }
}
