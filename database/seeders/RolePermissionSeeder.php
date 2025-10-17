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
            'lihat_dashboard',
            'lihat_data_customer_milenia',
            'lihat_data_customer_map',
            'lihat_transaksi_customer_map_pusat',
            'lihat_transaksi_customer_map_cabang',
            'lihat_transaksi_customer_milenia_pusat',
            'lihat_transaksi_customer_milenia_cabang',
            'lihat_penjualan_sales_milenia_pusat',
            'lihat_penjualan_sales_milenia_cabang',
            'lihat_penjualan_sales_map_pusat',
            'lihat_penjualan_sales_map_cabang',
            'lihat_data_pricelist_produk_milenia',
            'lihat_data_pricelist_produk_map',
            'kelola_peran',
            'kelola_hak_akses',
            'kelola_data_master',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $adminPusat_milenia = Role::firstOrCreate(['name' => 'admin_pusat_milenia', 'guard_name' => 'web']);
        $adminPusat_map = Role::firstOrCreate(['name' => 'admin_pusat_map', 'guard_name' => 'web']);
        $adminCabang_milenia = Role::firstOrCreate(['name' => 'admin_cabang_milenia', 'guard_name' => 'web']);
        $adminCabang_map = Role::firstOrCreate(['name' => 'admin_cabang_map', 'guard_name' => 'web']);

        // Assign permissions
        $owner->syncPermissions(Permission::all());

        $adminPusat_milenia->syncPermissions([
            'lihat_dashboard',
            'kelola_data_master',
            'lihat_data_customer_milenia',
            'lihat_data_customer_map',
            'lihat_transaksi_customer_map_pusat',
            'lihat_transaksi_customer_map_cabang',
            'lihat_transaksi_customer_milenia_pusat',
            'lihat_transaksi_customer_milenia_cabang',
            'lihat_penjualan_sales_milenia_pusat',
            'lihat_penjualan_sales_milenia_cabang',
            'lihat_penjualan_sales_map_pusat',
            'lihat_penjualan_sales_map_cabang',
            'lihat_data_pricelist_produk_milenia',
            'lihat_data_pricelist_produk_map'
        ]);

        $adminCabang_milenia->syncPermissions([
            'lihat_dashboard',
            'lihat_data_customer_milenia',
            'lihat_transaksi_customer_milenia_cabang',
            'lihat_penjualan_sales_milenia_cabang',
            'lihat_data_pricelist_produk_milenia',
        ]);

        $adminPusat_map->syncPermissions([
            'lihat_dashboard',
            'lihat_data_customer_map',
            'lihat_transaksi_customer_map_pusat',
            'lihat_transaksi_customer_map_cabang',
            'lihat_penjualan_sales_map_pusat',
            'lihat_penjualan_sales_map_cabang',
            'lihat_data_pricelist_produk_map',
        ]);

        $this->command->info('✅ RolePermissionSeeder selesai dijalankan.');
    }
}
