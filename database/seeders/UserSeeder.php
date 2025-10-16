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
        $admin_pusat  = Role::firstOrCreate(['name' => 'admin_pusat', 'guard_name' => 'web']);
        $admin_cabang_milenia = Role::firstOrCreate(['name' => 'admin_cabang_milenia', 'guard_name' => 'web']);
        $admin_cabang_map = Role::firstOrCreate(['name' => 'admin_cabang_map', 'guard_name' => 'web']);


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
        $targetIds = [7605, 42, 7432, 7743];

        $adminPusatIds  = [7605];
        $adminCabangMap = [42];
        $adminCabangMilenia = [7432, 7743];

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
                $user->syncRoles([$admin_pusat->name]);
            } elseif (in_array($k->ID, $adminCabangMilenia)) {
                $user->syncRoles([$admin_cabang_milenia->name]);
            } elseif (in_array($k->ID, $adminCabangMap)) {
                $user->syncRoles([$admin_cabang_map->name]);
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
