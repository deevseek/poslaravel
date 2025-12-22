<?php

return [
    'central_connection' => env('DB_CONNECTION', 'mysql'),
    'tenant_connection' => 'tenant',
    'central_domains' => [
        env('APP_DOMAIN', 'profesionalservis.my.id'),
    ],
    'tenant_database_prefix' => env('TENANT_DB_PREFIX', 'tenant_'),
    'tenant_migrations_paths' => [
        database_path('migrations'),
        database_path('migrations/tenant'),
    ],
    'seeders' => [
        Database\Seeders\RolesAndPermissionsSeeder::class,
    ],
];
