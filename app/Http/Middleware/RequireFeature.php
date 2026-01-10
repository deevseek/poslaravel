<?php

namespace App\Http\Middleware;

use App\Support\SubscriptionFeatureService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireFeature
{
    public function __construct(private SubscriptionFeatureService $featureService)
    {
    }

    public function handle(Request $request, Closure $next, string ...$features): Response
    {
        foreach ($features as $feature) {
            if (! $this->featureService->hasFeature($feature)) {
                // Tolak akses jika fitur paket tidak tersedia.
                abort(403, 'Fitur tidak tersedia pada paket langganan Anda.');
            }
        }

        return $next($request);
    }
}
