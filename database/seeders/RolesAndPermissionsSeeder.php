<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect([
            ['name' => 'Lihat dashboard', 'slug' => 'view_dashboard'],
            ['name' => 'Kelola pengguna', 'slug' => 'manage_users'],
            ['name' => 'Kelola roles', 'slug' => 'manage_roles'],
            ['name' => 'Kelola permissions', 'slug' => 'manage_permissions'],
            ['name' => 'Kelola inventory', 'slug' => 'manage_inventory'],
            ['name' => 'Proses penjualan', 'slug' => 'process_sales'],
            ['name' => 'Kelola service', 'slug' => 'manage_service'],
            ['name' => 'Lihat laporan', 'slug' => 'view_reports'],
        ])->map(fn ($permission) => Permission::firstOrCreate(
            ['slug' => $permission['slug']],
            ['name' => $permission['name']]
        ));

        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator', 'description' => 'Akses penuh ke seluruh fitur']
        );

        $adminRole->permissions()->sync($permissions->pluck('id'));

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@pos.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );

        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
