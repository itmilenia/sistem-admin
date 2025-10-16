<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat permissions dasar
        $permissions = [
            'view_dashboard',
            'view_customer_milenia',
            'view_customer_map',
            'view_customer_transaction',
            'view_salesperson_sales',
            'manage_roles',
            'manage_permissions',
            'manage_master',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $adminPusat = Role::firstOrCreate(['name' => 'admin_pusat', 'guard_name' => 'web']);
        $adminCabang_milenia = Role::firstOrCreate(['name' => 'admin_cabang_milenia', 'guard_name' => 'web']);
        $adminCabang_map = Role::firstOrCreate(['name' => 'admin_cabang_map', 'guard_name' => 'web']);

        // Assign permissions
        $owner->syncPermissions(Permission::all());

        $adminPusat->syncPermissions([
            'view_dashboard',
            'manage_master',
            'view_dashboard',
            'view_customer_milenia',
            'view_customer_map',
            'view_customer_transaction',
            'view_salesperson_sales'
        ]);

        $adminCabang_milenia->syncPermissions([
            'view_dashboard',
            'view_customer_milenia',
            'view_customer_transaction',
            'view_salesperson_sales'
        ]);

        $adminCabang_map->syncPermissions([
            'view_dashboard',
            'view_customer_map',
            'view_customer_transaction',
            'view_salesperson_sales'
        ]);

        $this->command->info('✅ RolePermissionSeeder selesai dijalankan.');
    }
}
