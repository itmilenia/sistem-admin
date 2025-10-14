<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name', 'asc')->get();
        return view('pages.master.permission.index', compact('permissions'));
    }

    public function create()
    {
        return view('pages.master.permission.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ], [
            'name.required' => 'Nama permission tidak boleh kosong.',
            'name.unique' => 'Nama permission ini sudah ada.',
        ]);

        DB::beginTransaction();
        try {
            Permission::create(['name' => $validated['name']]);
            DB::commit();

            return redirect()->route('master-permission.index')->with('success', 'Permission baru berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal menyimpan permission: ' . $th->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id) // Route Model Binding
    {
        $permission = Permission::findOrFail($id);

        return view('pages.master.permission.edit', compact('permission'));
    }

    public function update(Request $request)
    {
        $permission = Permission::findOrFail($request->id);

        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        DB::beginTransaction();
        try {
            $permission->update(['name' => $validated['name']]);
            DB::commit();

            return redirect()->route('master-permission.index')->with('success', 'Permission berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal update permission [ID: ' . $permission->id . ']: ' . $th->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // Mencegah penghapusan jika permission masih digunakan oleh role atau user
        if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
            return back()->with('error', 'Permission ini masih digunakan oleh role atau user.');
        }

        DB::beginTransaction();
        try {
            $permission->delete();
            DB::commit();

            return redirect()->route('master-permission.index')->with('success', 'Permission berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal menghapus permission [ID: ' . $permission->id . ']: ' . $th->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
