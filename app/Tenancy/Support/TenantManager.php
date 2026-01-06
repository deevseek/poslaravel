<?php

namespace App\Tenancy\Support;

use App\Tenancy\Models\Subscription;
use App\Tenancy\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class TenantManager
{
    protected ?Tenant $tenant = null;

    public function __construct(
        protected Application $app,
        protected DatabaseManager $databaseManager
    ) {
    }

    public function resolve(Request $request): ?Tenant
    {
        $host = $this->normalizeHost($request->getHost());
        $baseDomains = $this->normalizeDomains(Config::get('tenancy.central_domains', []));

        if ($this->isCentralHost($host, $baseDomains)) {
            return null;
        }

        $subdomain = $this->extractSubdomain($host, $baseDomains);

        if (! $subdomain) {
            return null;
        }

        return $this->tenant = Tenant::query()->where('subdomain', $subdomain)->first();
    }

    public function isCentralHost(string $host, ?array $baseDomains = null): bool
    {
        $host = $this->normalizeHost($host);
        $baseDomains = $this->normalizeDomains($baseDomains ?? Config::get('tenancy.central_domains', []));

        if (in_array($host, $baseDomains, true)) {
            return true;
        }

        $subdomain = $this->extractSubdomain($host, $baseDomains);

        if (! $subdomain) {
            return false;
        }

        $centralSubdomains = Config::get('tenancy.central_subdomains', []);

        return in_array($subdomain, $centralSubdomains, true);
    }

    public function current(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function isSubscriptionActive(): bool
    {
        if (! $this->tenant) {
            return false;
        }

        /** @var Subscription|null $subscription */
        $subscription = Subscription::query()
            ->where('tenant_id', $this->tenant->id)
            ->latest('end_date')
            ->first();

        if (! $subscription) {
            return false;
        }

        if ($subscription->status === 'suspended') {
            return false;
        }

        return $subscription->end_date === null || Carbon::now()->lte($subscription->end_date);
    }

    public function switchToTenantConnection(Tenant $tenant): void
    {
        $databaseName = $tenant->database_name;
        Config::set('database.default', Config::get('tenancy.tenant_connection', 'tenant'));
        Config::set('database.connections.tenant.database', $databaseName);

        $this->databaseManager->purge('tenant');
        $this->databaseManager->reconnect('tenant');
        $this->app->forgetInstance('db');
        $this->app->bind('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });
    }

    protected function extractSubdomain(string $host, array $baseDomains): ?string
    {
        foreach ($baseDomains as $baseDomain) {
            if (Str::endsWith($host, $baseDomain)) {
                $possible = Str::before($host, '.' . $baseDomain);
                return $possible !== $host ? $possible : null;
            }
        }

        return null;
    }

    protected function normalizeDomains(array $baseDomains): array
    {
        return array_values(array_filter(array_map(function ($domain) {
            if (! is_string($domain)) {
                return null;
            }

            $host = $this->normalizeHost($domain);

            return $host !== '' ? $host : null;
        }, $baseDomains)));
    }

    protected function normalizeHost(string $host): string
    {
        $host = trim($host);

        if ($host === '') {
            return '';
        }

        if (! Str::startsWith($host, ['http://', 'https://'])) {
            $host = 'http://' . $host;
        }

        $normalized = parse_url($host, PHP_URL_HOST);

        return strtolower($normalized ?: $host);
    }
}
