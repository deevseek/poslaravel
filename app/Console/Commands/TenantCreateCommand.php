<?php

namespace App\Console\Commands;

use App\Tenancy\Services\TenantProvisioningService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenantCreateCommand extends Command
{
    protected $signature = 'tenant:create {name} {subdomain} {email} {password} {--plan=}';

    protected $description = 'Provision a new tenant with database and admin user';

    public function handle(TenantProvisioningService $service): int
    {
        $payload = [
            'name' => $this->argument('name'),
            'subdomain' => $this->argument('subdomain'),
            'email' => $this->argument('email'),
            'password' => $this->argument('password'),
            'plan_id' => $this->option('plan'),
        ];

        $validator = Validator::make($payload, [
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'alpha_dash', 'max:50', Rule::unique('tenants', 'subdomain')],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $tenant = $service->provision($payload);
        $this->info("Tenant {$tenant->name} created with database {$tenant->database_name}.");

        return self::SUCCESS;
    }
}
