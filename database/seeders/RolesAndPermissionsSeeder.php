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
            ['name' => 'Lihat Dashboard', 'slug' => 'dashboard.view'],
            ['name' => 'Akses POS', 'slug' => 'pos.access'],
            ['name' => 'Buat Transaksi POS', 'slug' => 'pos.create'],
            ['name' => 'Cetak Struk POS', 'slug' => 'pos.print'],
            ['name' => 'Akses Service', 'slug' => 'service.access'],
            ['name' => 'Buat Service', 'slug' => 'service.create'],
            ['name' => 'Perbarui Status Service', 'slug' => 'service.update_status'],
            ['name' => 'Tambah Sparepart Service', 'slug' => 'service.add_sparepart'],
            ['name' => 'Lihat Inventory', 'slug' => 'inventory.view'],
            ['name' => 'Tambah Inventory', 'slug' => 'inventory.create'],
            ['name' => 'Perbarui Inventory', 'slug' => 'inventory.update'],
            ['name' => 'Hapus Inventory', 'slug' => 'inventory.delete'],
            ['name' => 'Penyesuaian Stok', 'slug' => 'inventory.adjust_stock'],
            ['name' => 'Lihat Pembelian', 'slug' => 'purchase.view'],
            ['name' => 'Buat Pembelian', 'slug' => 'purchase.create'],
            ['name' => 'Setujui Pembelian', 'slug' => 'purchase.approve'],
            ['name' => 'Kelola Customer', 'slug' => 'customer.manage'],
            ['name' => 'Kelola Supplier', 'slug' => 'supplier.manage'],
            ['name' => 'Lihat Garansi', 'slug' => 'warranty.view'],
            ['name' => 'Klaim Garansi', 'slug' => 'warranty.claim'],
            ['name' => 'Setujui Garansi', 'slug' => 'warranty.approve'],
            ['name' => 'Lihat Keuangan', 'slug' => 'finance.view'],
            ['name' => 'Catat Pemasukan', 'slug' => 'finance.create_income'],
            ['name' => 'Catat Pengeluaran', 'slug' => 'finance.create_expense'],
            ['name' => 'Tutup Kas', 'slug' => 'finance.close_cash'],
            ['name' => 'Laporan Keuangan', 'slug' => 'finance.report'],
            ['name' => 'Kelola HRD', 'slug' => 'hrd.manage'],
            ['name' => 'Kelola Payroll', 'slug' => 'payroll.manage'],
            ['name' => 'Lihat Laporan', 'slug' => 'report.view'],
            ['name' => 'Ekspor Laporan', 'slug' => 'report.export'],
            ['name' => 'Lihat Pengaturan', 'slug' => 'settings.view'],
            ['name' => 'Ubah Pengaturan', 'slug' => 'settings.update'],
            ['name' => 'Notifikasi Status WhatsApp', 'slug' => 'whatsapp.status_notify'],
            ['name' => 'Broadcast WhatsApp', 'slug' => 'whatsapp.broadcast'],
            ['name' => 'Kelola Template WhatsApp', 'slug' => 'whatsapp.template_manage'],
            ['name' => 'Lihat Log WhatsApp', 'slug' => 'whatsapp.log_view'],
            ['name' => 'Kelola User', 'slug' => 'user.manage'],
            ['name' => 'Kelola Role', 'slug' => 'role.manage'],
            ['name' => 'Kelola Permission', 'slug' => 'permission.manage'],
            ['name' => 'Kelola Tenant', 'slug' => 'tenant.manage'],
        ])->mapWithKeys(fn ($permission) => [
            $permission['slug'] => Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                ['name' => $permission['name']]
            ),
        ]);

        $allPermissions = $permissions->pluck('id');

        $roles = [
            'owner' => [
                'name' => 'Owner',
                'description' => 'Pemilik usaha dengan akses penuh',
                'permissions' => $allPermissions,
            ],
            'admin' => [
                'name' => 'Admin',
                'description' => 'Pengelola sistem dengan akses penuh',
                'permissions' => $allPermissions,
            ],
            'kasir' => [
                'name' => 'Kasir',
                'description' => 'Fokus pada transaksi kasir dan pencatatan keuangan',
                'permissions' => $permissions->only([
                    'dashboard.view',
                    'pos.access',
                    'pos.create',
                    'pos.print',
                    'customer.manage',
                    'inventory.view',
                    'purchase.view',
                    'purchase.create',
                    'finance.view',
                    'finance.create_income',
                    'finance.create_expense',
                    'finance.close_cash',
                    'finance.report',
                    'report.view',
                    'settings.view',
                    'whatsapp.status_notify',
                    'warranty.view',
                ])->pluck('id'),
            ],
            'teknisi' => [
                'name' => 'Teknisi',
                'description' => 'Menangani layanan service dan garansi',
                'permissions' => $permissions->only([
                    'dashboard.view',
                    'service.access',
                    'service.create',
                    'service.update_status',
                    'service.add_sparepart',
                    'inventory.view',
                    'inventory.adjust_stock',
                    'inventory.delete',
                    'warranty.view',
                    'warranty.claim',
                    'report.view',
                    'whatsapp.status_notify',
                ])->pluck('id'),
            ],
            'marketing' => [
                'name' => 'Marketing',
                'description' => 'Berfokus pada hubungan pelanggan dan kampanye',
                'permissions' => $permissions->only([
                    'dashboard.view',
                    'customer.manage',
                    'supplier.manage',
                    'report.view',
                    'report.export',
                    'whatsapp.broadcast',
                    'whatsapp.template_manage',
                    'whatsapp.log_view',
                    'whatsapp.status_notify',
                    'settings.view',
                ])->pluck('id'),
            ],
            'hrd' => [
                'name' => 'HRD',
                'description' => 'Mengelola data karyawan dan administrasi HRD',
                'permissions' => $permissions->only([
                    'dashboard.view',
                    'hrd.manage',
                    'payroll.manage',
                    'report.view',
                    'settings.view',
                ])->pluck('id'),
            ],
            'payroll' => [
                'name' => 'Payroll',
                'description' => 'Mengelola proses penggajian karyawan',
                'permissions' => $permissions->only([
                    'dashboard.view',
                    'payroll.manage',
                    'report.view',
                ])->pluck('id'),
            ],
        ];

        foreach ($roles as $slug => $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => $roleData['name'], 'description' => $roleData['description']]
            );

            $role->permissions()->sync($roleData['permissions']);
        }

        if ($this->isCentralConnection()) {
            $ownerRoleId = Role::where('slug', 'owner')->value('id');
            $adminRoleId = Role::where('slug', 'admin')->value('id');

            $adminUser = User::firstOrCreate(
                ['email' => 'nugraha.deev@gmail.com'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('Kmzwa8awa@'),
                    'email_verified_at' => now(),
                ]
            );

            $ownerUser = User::firstOrCreate(
                ['email' => 'owner@pos.com'],
                [
                    'name' => 'Owner',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $adminUser->roles()->syncWithoutDetaching([$adminRoleId]);
            $ownerUser->roles()->syncWithoutDetaching([$ownerRoleId]);
        }
    }

    protected function isCentralConnection(): bool
    {
        return config('database.default') === config('tenancy.central_connection', 'mysql');
    }
}
