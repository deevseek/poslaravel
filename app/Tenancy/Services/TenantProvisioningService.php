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
use RuntimeException;
use Throwable;

class TenantProvisioningService
{
    public function __construct(private TenantManager $tenantManager)
    {
    }

    public function provision(array $payload): Tenant
    {
        $centralConnection = Config::get('tenancy.central_connection', 'mysql');
        $subdomain = Str::slug($payload['subdomain']);
        $databaseName = $this->makeDatabaseName($subdomain);

        $this->createTenantDatabase($databaseName, $centralConnection);
        $this->runMigrations($databaseName);
        $this->seedTenant($databaseName, $payload, $centralConnection);

        return DB::connection($centralConnection)->transaction(function () use ($payload, $databaseName, $subdomain) {
            $tenant = Tenant::create([
                'name' => $payload['name'],
                'subdomain' => $subdomain,
                'database_name' => $databaseName,
                'status' => 'active',
                'plan_id' => $payload['plan_id'] ?? null,
            ]);

            $this->attachSubscription($tenant, $payload['plan_id'] ?? null);

            return $tenant;
        });
    }

    protected function createTenantDatabase(string $databaseName, string $centralConnection): void
    {
        DB::connection($centralConnection)->statement(
            "CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    }

    protected function runMigrations(string $databaseName): void
    {
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');

        $this->callArtisanOrFail('migrate', [
            '--database' => 'tenant',
            '--path' => Config::get('tenancy.tenant_migrations_paths'),
            '--realpath' => true,
            '--force' => true,
        ]);
    }

    protected function seedTenant(string $databaseName, array $payload, string $centralConnection): void
    {
        $manager = $this->tenantManager;
        $manager->switchToTenantConnection(new Tenant(['database_name' => $databaseName]));
        $tenantConnection = Config::get('tenancy.tenant_connection', 'tenant');

        foreach (Config::get('tenancy.seeders', []) as $seeder) {
            $this->callArtisanOrFail('db:seed', [
                '--class' => $seeder,
                '--database' => 'tenant',
                '--force' => true,
            ]);
        }

        $ownerRoleId = Role::on($tenantConnection)->where('slug', 'owner')->value('id');

        $user = User::on($tenantConnection)->create([
            'name' => $payload['admin_name'] ?? $payload['name'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        if ($ownerRoleId) {
            $user->roles()->sync([$ownerRoleId]);
        }

        Config::set('database.default', $centralConnection);
        DB::purge($centralConnection);
        DB::reconnect($centralConnection);
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

    protected function callArtisanOrFail(string $command, array $arguments = []): void
    {
        $exitCode = Artisan::call($command, $arguments);

        if ($exitCode !== 0) {
            $output = trim(Artisan::output());
            $message = $output !== '' ? $output : "Command {$command} failed.";

            throw new RuntimeException($message);
        }
    }
}
