<?php

namespace App\Http\Controllers\Master;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        // Ambil semua user yang memiliki role, kecuali 'owner'
        $employees = User::where('Aktif', 1)
            ->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'owner');
            })
            ->with('roles') // Eager load roles untuk performa
            ->orderBy('Nama', 'asc')
            ->get();

        return view('pages.master.user.index', [
            'employees' => $employees,
        ]);
    }

    public function create()
    {
        // Ambil ID user yang sudah ada untuk filtering
        $existingUserIds = User::pluck('ID')
            ->where('Aktif', 1)
            ->all();

        // Ambil semua karyawan dari HRD yang belum menjadi user
        $newEmployees = DB::connection('dbhrd')->table('trkaryawan')
            ->select('ID', 'Nama')
            ->where('Aktif', 1)
            ->whereNotIn('ID', $existingUserIds)
            ->orderBy('Nama')
            ->get();

        // Ambil semua role yang bisa di-assign (kecuali owner)
        $assignableRoles = Role::where('name', '!=', 'owner')
            ->orderBy('name', 'asc')
            ->get();

        return view('pages.master.user.create', [
            'newEmployees' => $newEmployees,
            'assignableRoles' => $assignableRoles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'IDs' => ['required', 'array', 'min:1'],
                'IDs.*' => ['distinct', 'integer'],
                'role' => ['required', 'string', 'exists:roles,name'],
            ],
            [
                'IDs.required' => 'Pilih minimal satu karyawan.',
                'IDs.*.distinct' => 'ID karyawan tidak boleh duplikat.',
                'role.required' => 'Role harus dipilih.',
                'role.exists' => 'Role yang dipilih tidak valid.',
            ]
        );

        $roleNameToAssign = $validated['role'];

        // Ambil data dari HRD hanya untuk ID yang dipilih
        $selectedEmployees = DB::connection('dbhrd')->table('trkaryawan')
            ->whereIn('ID', $validated['IDs'])
            ->where('Aktif', 1)
            ->get();

        // Cek lagi untuk memastikan tidak ada race condition
        $existingIds = User::whereIn('ID', $validated['IDs'])
            ->where('Aktif', 1)
            ->pluck('ID')
            ->all();

        if (!empty($existingIds)) {
            return back()->withInput()->with('error', 'ID karyawan ' . implode(', ', $existingIds) . ' sudah ada dalam database.');
        }

        foreach ($selectedEmployees as $employee) {
            $user = User::updateOrCreate(
                ['ID' => $employee->ID],
                [
                    'Nama' => $employee->Nama,
                    'Jabatan' => $employee->Jabatan ?? null,
                    'Divisi' => $employee->Divisi ?? null,
                    'Alamat_dom' => $employee->Alamat_dom ?? null,
                    'Aktif' => 1,
                    'uname' => $employee->uname ?? null,
                    'pwd' => $employee->pwd ?? null,
                    'lvl' => $employee->lvl ?? null,
                    'abs' => $employee->abs ?? null,
                    'statuskar' => $employee->statuskar ?? null,
                    'email_karyawan' => $employee->email_karyawan ?? null,
                    'email_atasan' => $employee->email_atasan ?? null,
                ]
            );

            // Assign role menggunakan Spatie
            $user->syncRoles([$roleNameToAssign]);

            $role = Role::where('name', $roleNameToAssign)->first();
            if ($role) {
                $user->syncPermissions($role->permissions);
            }
        }

        return redirect()->route('master-user.index')->with('success', 'Karyawan berhasil ditambahkan sebagai user.');
    }

    public function edit($id)
    {
        $employee = User::with(['roles', 'permissions'])->findOrFail($id);

        // Ambil semua role yang bisa di-assign (kecuali owner)
        $assignableRoles = Role::where('name', '!=', 'owner')
            ->orderBy('name', 'asc')
            ->get();

        // Ambil semua permission untuk form
        $allPermissions = Permission::orderBy('name', 'asc')->get();

        return view('pages.master.user.edit', [
            'employee' => $employee,
            'assignableRoles' => $assignableRoles,
            'allPermissions' => $allPermissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        DB::beginTransaction();
        try {
            // Mengganti role user
            $user->syncRoles($validated['role']);

            // Mengganti direct permissions
            $user->syncPermissions($validated['permissions'] ?? []);

            DB::commit();

            return redirect()->route('master-user.index')->with('success', 'Data user berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal update user [ID: ' . $id . ']: ' . $th->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function syncEmployeeData()
    {
        $httpcode = 500;

        try {
            Log::info('Mulai melakukan sinkronisasi data karyawan (update-only).');

            $apiUrl = config('services.hrd.api_url', 'http://192.168.0.8/hrd-milenia/API/karyawan/getAllEmployee.php');
            $apiKey = config('services.hrd.api_key', 'LxPNcX1EMScOV%zAVgTbY^ICbxUF8Pk@aZYTsmZcus57!uxgDGmxs!hjljN8');

            $http = Http::withOptions([
                'http_errors'     => false,
                'allow_redirects' => true,
            ])
                ->timeout(60)
                ->asForm();

            $res = $http->post($apiUrl, [
                'API_KEY' => $apiKey,
            ]);

            $httpcode = $res->status();
            $response = $res->body();

            if ($httpcode !== 200) {
                Log::error("Request API gagal, kode HTTP: {$httpcode}, Response: {$response}");
                return response()->json('Gagal memanggil API', $httpcode);
            }

            $decoded = json_decode($response, true);
            if (!is_array($decoded) || !isset($decoded['data']) || !is_array($decoded['data'])) {
                Log::error('Format respons API tidak sesuai.');
                return response()->json('Format respons API tidak sesuai', 500);
            }

            // === 1) Filter hanya Aktif = 1 ===
            $apiItems = collect($decoded['data'])
                ->filter(fn($it) => isset($it['Aktif']) && (int)$it['Aktif'] === 1)
                ->values();

            if ($apiItems->isEmpty()) {
                Log::info('Tidak ada data aktif dari API.');

                return redirect()->route('master-user.index')->with('error', 'Tidak ada data karyawan aktif dari API untuk disinkronisasi.');
            }

            // === 2) Ambil hanya ID yang sudah ada di users (agar tidak create) ===
            $apiIds      = $apiItems->pluck('ID')->filter()->unique();
            $existingIds = User::whereIn('ID', $apiIds)->pluck('ID')->all();

            if (empty($existingIds)) {
                Log::info('Tidak ada ID API yang cocok di tabel users.');

                return redirect()->route('master-user.index')->with('error', 'Tidak ada ID karyawan yang cocok di tabel untuk diupdate.');
            }

            // === 3) Siapkan field yang boleh diupdate ===
            $fillable = array_flip((new User)->getFillable());
            $dateFields = [
                'ID',
                'Nama',
                'JK',
                'TmpLahir',
                'TglLahir',
                'Agama',
                'Pendidikan',
                'Alamat',
                'Alamat_dom',
                'Kota',
                'KodePos',
                'Telpon',
                'KTP',
                'Status',
                'JA',
                'TglMasuk',
                'TglLulus',
                'TglUpdate',
                'Aktif',
                'TglKeluar',
                'Jabatan',
                'Divisi',
                'Cabang',
                'Golongan',
                'jeniskar',
                'statuskar',
                'no_bpjs_tk',
                'no_bpjs_kes',
                'tgl_keper_bpjs',
                'statBpjs',
                'Atasan',
                'JamKerja',
                'total_telat',
                'NoSuratKerja',
                'NoSuratKerja2',
                'MasaBerlaku',
                'MasaBerlaku2',
                'TjMakan',
                'stat_makan',
                'NilaiTjMakan',
                'TjBBM',
                'NilaiTjBBM',
                'stat_BBM',
                'TjAsuransi',
                'TjAssEff',
                'TjAssPolis',
                'TjPengobatan',
                'TjKerajinan',
                'TjLembur',
                'SaldoTjPengobatan',
                'TjUmObMinggu',
                'IDMesin',
                'StatusNoPrick',
                'Pajak',
                'Npwp',
                'hak_cuti',
                'jml_cuti',
                'jml_off',
                'nokk',
                'email_karyawan',
                'email_atasan',
                'uname',
                'pwd',
                'lvl',
                'abs',
            ];

            DB::beginTransaction();

            foreach ($apiItems as $row) {
                // Lewati jika ID tidak ada di users
                if (!in_array($row['ID'], $existingIds, true)) {
                    Log::info("Lewati ID {$row['ID']} (tidak ada di users).");
                    continue;
                }

                // Normalisasi tanggal "0000-00-00" -> null
                foreach ($dateFields as $df) {
                    if (isset($row[$df]) && $row[$df] === '0000-00-00') {
                        $row[$df] = null;
                    }
                }

                // Default angka null -> 0
                if (!isset($row['SaldoTjPengobatan']) || $row['SaldoTjPengobatan'] === null) {
                    $row['SaldoTjPengobatan'] = 0;
                }

                // Batasi ke kolom fillable
                $payloadUpdate = array_intersect_key($row, $fillable);

                // Jangan timpa kredensial
                unset($payloadUpdate['uname'], $payloadUpdate['pwd'], $payloadUpdate['email']);

                // Pastikan ikut sinkron status Aktif (kalau ada di payload)
                if (isset($row['Aktif'])) {
                    $payloadUpdate['Aktif'] = (int)$row['Aktif'];
                }

                // Update only (tanpa create)
                User::where('ID', $row['ID'])
                    ->where('Aktif', 1)
                    ->update($payloadUpdate);
                Log::info("User ID {$row['ID']} diperbarui.");
            }

            DB::commit();

            Log::info('Sinkronisasi data karyawan selesai (update-only).');

            return redirect()->route('master-user.index')->with('success', 'Sinkronisasi data karyawan berhasil.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Terjadi kesalahan dalam sinkronisasi data: ' . $th->getMessage());
            return response()->json($th->getMessage(), $httpcode);
        }
    }

    public function delete($id)
    {
        $karyawan = User::findOrFail($id);
        $karyawan->Aktif = 0;
        $karyawan->save();

        return redirect()->route('master-user.index')->with('success', 'Karyawan berhasil dinonaktifkan.');
    }
}
