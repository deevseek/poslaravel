<?php

namespace App\Console\Commands;

use App\Tenancy\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantMigrateCommand extends Command
{
    protected $signature = 'tenant:migrate {--tenant=* : Run for specific tenant IDs} {--fresh}';

    protected $description = 'Run migrations for all tenants';

    public function handle(): int
    {
        $tenantIds = $this->option('tenant');
        $tenants = Tenant::query()
            ->when($tenantIds, fn ($query) => $query->whereIn('id', $tenantIds))
            ->get();

        foreach ($tenants as $tenant) {
            $this->line("Migrating tenant {$tenant->subdomain} ({$tenant->database_name})");

            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');

            Artisan::call('migrate' . ($this->option('fresh') ? ':fresh' : ''), [
                '--database' => 'tenant',
                '--path' => Config::get('tenancy.tenant_migrations_paths'),
                '--force' => true,
            ]);

            $this->line(Artisan::output());
        }

        return self::SUCCESS;
    }
}
