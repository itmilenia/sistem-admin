<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Pastikan roles ada
        $owner        = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $admin_pusat_milenia  = Role::firstOrCreate(['name' => 'admin_pusat_milenia', 'guard_name' => 'web']);
        $admin_pusat_map  = Role::firstOrCreate(['name' => 'admin_pusat_map', 'guard_name' => 'web']);
        $admin_cabang_milenia = Role::firstOrCreate(['name' => 'admin_cabang_milenia', 'guard_name' => 'web']);
        $admin_cabang_map = Role::firstOrCreate(['name' => 'admin_cabang_map', 'guard_name' => 'web']);
        $trainer_milenia = Role::firstOrCreate(['name' => 'trainer_milenia', 'guard_name' => 'web']);
        $trainer_map = Role::firstOrCreate(['name' => 'trainer_map', 'guard_name' => 'web']);
        $sales_milenia = Role::firstOrCreate(['name' => 'sales_milenia', 'guard_name' => 'web']);
        $sales_map = Role::firstOrCreate(['name' => 'sales_map', 'guard_name' => 'web']);
        $head_sales_milenia = Role::firstOrCreate(['name' => 'head_sales_milenia', 'guard_name' => 'web']);
        $head_sales_map = Role::firstOrCreate(['name' => 'head_sales_map', 'guard_name' => 'web']);


        // 1) Buat Super Admin lokal
        $superAdmin = User::updateOrCreate(
            ['ID' => 1],
            [
                'Nama'       => 'Super Admin',
                'Alamat_dom' => 'Milenia',
                'Aktif'      => 1,
                'uname'      => 'superadmin',
                'pwd'        => 'superadmin2011**',
                'lvl'        => 1,
                'abs'        => 1,
            ]
        );

        $superAdmin->refresh();

        $superAdmin->syncRoles([$owner->name]);

        // 2) Target ID dari HRD
        $targetIds = [7605, 42, 7432, 7743, 7311, 332, 7713, 7752, 7755, 1064, 7178, 7735, 7758];

        $adminPusatIds  = [7605];
        $adminPusatMap = [42];
        $adminCabangMilenia = [7432, 7743];
        $trainerMilenia = [332];
        $trainerMap = [7311];
        $salesMilenia = [7713, 7752, 7755];
        $salesMap = [7735, 7758];
        $headSalesMilenia = [7178];
        $headSalesMap = [1064];

        $karyawans = DB::connection('dbhrd')
            ->table('trkaryawan')
            ->whereIn('ID', $targetIds)
            ->get([
                'ID',
                'Nama',
                'Alamat_dom',
                'statuskar',
                'Jabatan',
                'Divisi',
                'Aktif',
                'uname',
                'pwd',
                'lvl',
                'abs'
            ]);

        if ($karyawans->isEmpty()) {
            $this->command->info('⚠️ Tidak ada data karyawan ditemukan dari dbhrd.');
            return;
        }

        foreach ($karyawans as $k) {
            $user = User::updateOrCreate(
                ['ID' => $k->ID],
                [
                    'Nama'       => $k->Nama,
                    'Alamat_dom' => $k->Alamat_dom,
                    'statuskar'  => $k->statuskar,
                    'Jabatan'    => $k->Jabatan,
                    'Divisi'     => $k->Divisi,
                    'Aktif'      => $k->Aktif,
                    'uname'      => $k->uname,
                    'pwd'        => $k->pwd,
                    'lvl'        => $k->lvl,
                    'abs'        => $k->ID,
                ]
            );

            // refresh model supaya getKey() benar
            $user->refresh();

            // debug (opsional) — bisa di-comment setelah OK
            $this->command->info("User created: ID={$user->ID}, getKey={$user->getKey()}");

            // assign role gunakan nama role
            if (in_array($k->ID, $adminPusatIds)) {
                $user->syncRoles([$admin_pusat_milenia->name]);
            } elseif (in_array($k->ID, $adminCabangMilenia)) {
                $user->syncRoles([$admin_cabang_milenia->name]);
            } elseif (in_array($k->ID, $adminPusatMap)) {
                $user->syncRoles([$admin_pusat_map->name]);
            } elseif (in_array($k->ID, $salesMilenia)) {
                $user->syncRoles([$sales_milenia->name]);
            } elseif (in_array($k->ID, $trainerMilenia)) {
                $user->syncRoles([$trainer_milenia->name]);
            } elseif (in_array($k->ID, $trainerMap)) {
                $user->syncRoles([$trainer_map->name]);
            } elseif (in_array($k->ID, $salesMap)) {
                $user->syncRoles([$sales_map->name]);
            } elseif (in_array($k->ID, $headSalesMilenia)) {
                $user->syncRoles([$head_sales_milenia->name]);
            } elseif (in_array($k->ID, $headSalesMap)) {
                $user->syncRoles([$head_sales_map->name]);
            }

            // cek langsung apakah pivot terisi
            $exists = DB::table('model_has_roles')
                ->where('model_type', get_class($user))
                ->where('model_id', $user->getKey())
                ->exists();

            $this->command->info("  -> pivot_exists: " . ($exists ? 'yes' : 'no'));
        }

        $this->command->info('✅ UserSeeder selesai.');
    }
}
