<?php

namespace App\Console\Commands;

use App\Tenancy\Models\Tenant;
use Illuminate\Console\Command;

class TenantDeactivateCommand extends Command
{
    protected $signature = 'tenant:deactivate {tenant : Tenant ID or subdomain}';

    protected $description = 'Suspend a tenant without deleting data';

    public function handle(): int
    {
        $identifier = $this->argument('tenant');
        $tenant = Tenant::query()
            ->where('id', $identifier)
            ->orWhere('subdomain', $identifier)
            ->first();

        if (! $tenant) {
            $this->error('Tenant not found');
            return self::FAILURE;
        }

        $tenant->status = 'suspended';
        $tenant->save();

        $this->info("Tenant {$tenant->subdomain} suspended.");

        return self::SUCCESS;
    }
}
