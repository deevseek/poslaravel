<?php

namespace App\Tenancy\Services;

use App\Models\Role;
use App\Models\User;
use App\Tenancy\Models\Subscription;
use App\Tenancy\Models\SubscriptionPlan;
use App\Tenancy\Models\Tenant;
use App\Tenancy\Support\TenantManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class TenantProvisioningService
{
    public function __construct(private TenantManager $tenantManager)
    {
    }

    public function provision(array $payload): Tenant
    {
        return DB::transaction(function () use ($payload) {
            $subdomain = Str::slug($payload['subdomain']);
            $databaseName = $this->makeDatabaseName($subdomain);

            $tenant = Tenant::create([
                'name' => $payload['name'],
                'subdomain' => $subdomain,
                'database_name' => $databaseName,
                'status' => 'active',
                'plan_id' => $payload['plan_id'] ?? null,
            ]);

            $this->createTenantDatabase($databaseName);
            $this->runMigrations($databaseName);
            $this->seedTenant($databaseName, $payload);
            $this->attachSubscription($tenant, $payload['plan_id'] ?? null);

            return $tenant;
        });
    }

    protected function createTenantDatabase(string $databaseName): void
    {
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    protected function runMigrations(string $databaseName): void
    {
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => Config::get('tenancy.tenant_migrations_paths'),
            '--force' => true,
        ]);
    }

    protected function seedTenant(string $databaseName, array $payload): void
    {
        $manager = $this->tenantManager;
        $manager->switchToTenantConnection(new Tenant(['database_name' => $databaseName]));

        foreach (Config::get('tenancy.seeders', []) as $seeder) {
            Artisan::call('db:seed', [
                '--class' => $seeder,
                '--database' => 'tenant',
                '--force' => true,
            ]);
        }

        $ownerRoleId = Role::where('slug', 'owner')->value('id');

        $user = User::create([
            'name' => $payload['admin_name'] ?? $payload['name'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        if ($ownerRoleId) {
            $user->roles()->sync([$ownerRoleId]);
        }
    }

    protected function attachSubscription(Tenant $tenant, ?int $planId): void
    {
        if (! $planId) {
            return;
        }

        $plan = SubscriptionPlan::find($planId);

        if (! $plan) {
            return;
        }

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);
    }

    protected function makeDatabaseName(string $subdomain): string
    {
        $prefix = Config::get('tenancy.tenant_database_prefix', 'tenant_');

        return $prefix . $subdomain;
    }
}
