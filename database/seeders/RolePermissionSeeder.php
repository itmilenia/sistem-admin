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
            'lihat_surat_penawaran_milenia',
            'lihat_surat_penawaran_map',
            'buat_surat_penawaran_milenia',
            'buat_surat_penawaran_map',
            'ubah_surat_penawaran_milenia',
            'ubah_surat_penawaran_map',
            'hapus_surat_penawaran_milenia',
            'hapus_surat_penawaran_map',
            'lihat_surat_agreement_milenia',
            'lihat_surat_agreement_map',
            'buat_surat_agreement_milenia',
            'buat_surat_agreement_map',
            'ubah_surat_agreement_milenia',
            'ubah_surat_agreement_map',
            'hapus_surat_agreement_milenia',
            'hapus_surat_agreement_map',
            'lihat_program_promo_milenia',
            'lihat_program_promo_map',
            'buat_program_promo_milenia',
            'buat_program_promo_map',
            'ubah_program_promo_milenia',
            'ubah_program_promo_map',
            'hapus_program_promo_milenia',
            'hapus_program_promo_map',
            'lihat_klaim_produk_milenia',
            'lihat_klaim_produk_map',
            'buat_klaim_produk_milenia',
            'buat_klaim_produk_map',
            'ubah_klaim_produk_milenia',
            'ubah_klaim_produk_map',
            'hapus_klaim_produk_milenia',
            'hapus_klaim_produk_map',
            'verifikasi_klaim_produk_milenia',
            'verifikasi_klaim_produk_map',
            'tanda_tangan_sales_klaim_produk_milenia',
            'tanda_tangan_sales_klaim_produk_map',
            'tanda_tangan_head_sales_klaim_produk_milenia',
            'tanda_tangan_head_sales_klaim_produk_map',
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
        $trainer_milenia = Role::firstOrCreate(['name' => 'trainer_milenia', 'guard_name' => 'web']);
        $trainer_map = Role::firstOrCreate(['name' => 'trainer_map', 'guard_name' => 'web']);
        $sales_milenia = Role::firstOrCreate(['name' => 'sales_milenia', 'guard_name' => 'web']);
        $sales_map = Role::firstOrCreate(['name' => 'sales_map', 'guard_name' => 'web']);
        $head_sales_milenia = Role::firstOrCreate(['name' => 'head_sales_milenia', 'guard_name' => 'web']);
        $head_sales_map = Role::firstOrCreate(['name' => 'head_sales_map', 'guard_name' => 'web']);

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
            'lihat_data_pricelist_produk_map',
            'lihat_surat_penawaran_milenia',
            'lihat_surat_penawaran_map',
            'buat_surat_penawaran_milenia',
            'buat_surat_penawaran_map',
            'ubah_surat_penawaran_milenia',
            'ubah_surat_penawaran_map',
            'hapus_surat_penawaran_milenia',
            'hapus_surat_penawaran_map',
            'lihat_surat_agreement_milenia',
            'lihat_surat_agreement_map',
            'buat_surat_agreement_milenia',
            'buat_surat_agreement_map',
            'ubah_surat_agreement_milenia',
            'ubah_surat_agreement_map',
            'hapus_surat_agreement_milenia',
            'hapus_surat_agreement_map',
            'lihat_program_promo_milenia',
            'lihat_program_promo_map',
            'buat_program_promo_milenia',
            'buat_program_promo_map',
            'ubah_program_promo_milenia',
            'ubah_program_promo_map',
            'hapus_program_promo_milenia',
            'hapus_program_promo_map',
            'lihat_klaim_produk_milenia',
            'lihat_klaim_produk_map',
            'buat_klaim_produk_milenia',
            'buat_klaim_produk_map',
            'ubah_klaim_produk_milenia',
            'ubah_klaim_produk_map',
            'hapus_klaim_produk_milenia',
            'hapus_klaim_produk_map',
        ]);

        $adminCabang_milenia->syncPermissions([
            'lihat_dashboard',
            'lihat_data_customer_milenia',
            'lihat_transaksi_customer_milenia_cabang',
            'lihat_penjualan_sales_milenia_cabang',
            'lihat_data_pricelist_produk_milenia',
            'lihat_surat_penawaran_milenia',
            'buat_surat_penawaran_milenia',
            'ubah_surat_penawaran_milenia',
            'hapus_surat_penawaran_milenia',
            'lihat_surat_agreement_milenia',
            'buat_surat_agreement_milenia',
            'ubah_surat_agreement_milenia',
            'hapus_surat_agreement_milenia',
            'lihat_program_promo_milenia',
            'buat_program_promo_milenia',
            'ubah_program_promo_milenia',
            'hapus_program_promo_milenia',
            'lihat_klaim_produk_milenia',
            'buat_klaim_produk_milenia',
            'ubah_klaim_produk_milenia',
            'hapus_klaim_produk_milenia',
        ]);

        $adminPusat_map->syncPermissions([
            'lihat_dashboard',
            'lihat_data_customer_map',
            'lihat_transaksi_customer_map_pusat',
            'lihat_transaksi_customer_map_cabang',
            'lihat_penjualan_sales_map_pusat',
            'lihat_penjualan_sales_map_cabang',
            'lihat_data_pricelist_produk_map',
            'lihat_surat_penawaran_map',
            'buat_surat_penawaran_map',
            'ubah_surat_penawaran_map',
            'hapus_surat_penawaran_map',
            'lihat_surat_agreement_map',
            'buat_surat_agreement_map',
            'ubah_surat_agreement_map',
            'hapus_surat_agreement_map',
            'lihat_program_promo_map',
            'buat_program_promo_map',
            'ubah_program_promo_map',
            'hapus_program_promo_map',
            'lihat_klaim_produk_map',
            'buat_klaim_produk_map',
            'ubah_klaim_produk_map',
            'hapus_klaim_produk_map',
        ]);

        $trainer_milenia->syncPermissions([
            'lihat_klaim_produk_milenia',
            'verifikasi_klaim_produk_milenia',
        ]);

        $trainer_map->syncPermissions([
            'lihat_klaim_produk_map',
            'verifikasi_klaim_produk_map',
        ]);

        $head_sales_milenia->syncPermissions([
            'lihat_klaim_produk_milenia',
            'tanda_tangan_head_sales_klaim_produk_milenia',
        ]);

        $head_sales_map->syncPermissions([
            'lihat_klaim_produk_map',
            'tanda_tangan_head_sales_klaim_produk_map',
        ]);

        $sales_milenia->syncPermissions([
            'lihat_klaim_produk_milenia',
            'tanda_tangan_sales_klaim_produk_milenia',        ]);

        $sales_map->syncPermissions([
            'lihat_klaim_produk_map',
            'tanda_tangan_sales_klaim_produk_map',
        ]);

        $this->command->info('✅ RolePermissionSeeder selesai dijalankan.');
    }
}
