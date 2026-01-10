<?php

namespace App\Http\Middleware;

use App\Tenancy\Support\TenantManager;
use Closure;
use Illuminate\Http\Request;

class CentralDomain
{
    public function __construct(private TenantManager $tenantManager)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->tenantManager->isCentralHost($request->getHost())) {
            abort(403);
        }

        return $next($request);
    }
}
