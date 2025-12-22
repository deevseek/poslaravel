<?php

namespace App\Console;

use App\Console\Commands\TenantCreateCommand;
use App\Console\Commands\TenantDeactivateCommand;
use App\Console\Commands\TenantMigrateCommand;
use App\Console\Commands\TenantSeedCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        TenantCreateCommand::class,
        TenantDeactivateCommand::class,
        TenantMigrateCommand::class,
        TenantSeedCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Define scheduled commands for billing, cleanups, etc.
    }
}
