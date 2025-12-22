<?php

namespace App\Console\Commands;

use App\Tenancy\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantSeedCommand extends Command
{
    protected $signature = 'tenant:seed {--tenant=* : Run for specific tenant IDs}';

    protected $description = 'Seed data for tenant databases';

    public function handle(): int
    {
        $tenantIds = $this->option('tenant');
        $tenants = Tenant::query()
            ->when($tenantIds, fn ($query) => $query->whereIn('id', $tenantIds))
            ->get();

        foreach ($tenants as $tenant) {
            $this->line("Seeding tenant {$tenant->subdomain} ({$tenant->database_name})");

            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');

            foreach (Config::get('tenancy.seeders', []) as $seeder) {
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--database' => 'tenant',
                    '--force' => true,
                ]);
            }

            $this->line(Artisan::output());
        }

        return self::SUCCESS;
    }
}
