<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', '!=', 'owner')
            ->withCount(['users as active_users_count' => function ($query) {
                $query->where('Aktif', 1);
            }])
            ->orderBy('name', 'asc')
            ->get();


        return view('pages.master.role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        return view('pages.master.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Nama role tidak boleh kosong.',
            'name.unique' => 'Nama role ini sudah digunakan.',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $validated['name']]);

            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('master-role.index')->with('success', 'Role baru berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal menyimpan role: ' . $th->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        // Mencegah user mengedit role 'owner'
        if ($role->name === 'owner') {
            abort(403, 'Role Owner tidak dapat diedit.');
        }

        $permissions = Permission::orderBy('name', 'asc')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('pages.master.role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Mencegah user mengedit role 'owner'
        if ($role->name === 'owner') {
            abort(403, 'Role Owner tidak dapat diedit.');
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);

            DB::commit();

            return redirect()->route('master-role.index')->with('success', 'Role berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal update role [ID: ' . $role->id . ']: ' . $th->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Mencegah user menghapus role penting
        if ($role->name === 'owner' || $role->name === 'admin_pusat') {
            return back()->with('error', 'Role penting tidak dapat dihapus.');
        }

        // Mencegah penghapusan jika role masih digunakan oleh user
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role ini masih digunakan oleh user.');
        }

        $role->delete();

        return redirect()->route('master-role.index')->with('success', 'Role berhasil dihapus.');
    }
}
