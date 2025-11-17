<?php

namespace App\Http\Controllers\Master;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::orderBy('is_active', 'desc')
            ->orderBy('tax_name', 'asc')
            ->get();

        return view('pages.master.tax.index', compact('taxes'));
    }

    public function create()
    {
        return view('pages.master.tax.create');
    }

    public function store(Request $request)
    {
        // Validasi berdasarkan field di Model dan Migrasi
        $validated = $request->validate([
            'tax_name' => 'required|string|unique:tr_tax,tax_name',
            'tax_rate' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ], [
            'tax_name.required' => 'Nama pajak tidak boleh kosong.',
            'tax_name.unique' => 'Nama pajak ini sudah ada.',
            'tax_rate.required' => 'Tarif pajak tidak boleh kosong.',
            'tax_rate.numeric' => 'Tarif pajak harus berupa angka.',
            'is_active.required' => 'Status tidak boleh kosong.',
        ]);

        // hanya boleh ada 1 pajak aktif
        if (Tax::where('is_active', true)->exists()) {
            return back()->withInput()->with('error', 'Pajak aktif sudah ada.');
        }

        DB::beginTransaction();
        try {
            $validated['created_by'] = Auth::id();

            Tax::create($validated);

            DB::commit();

            // Redirect ke halaman index master-tax
            return redirect()->route('master-tax.index')->with('success', 'Pajak baru berhasil ditambahkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal menyimpan pajak: ' . $th->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        $tax = Tax::findOrFail($id);

        return view('pages.master.tax.edit', compact('tax'));
    }

    public function update(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);

        $validated = $request->validate([
            'tax_name' => 'required|string|unique:tr_tax,tax_name,' . $tax->id,
            'tax_rate' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ], [
            'tax_name.required' => 'Nama pajak tidak boleh kosong.',
            'tax_name.unique' => 'Nama pajak ini sudah ada.',
            'tax_rate.required' => 'Tarif pajak tidak boleh kosong.',
            'tax_rate.numeric' => 'Tarif pajak harus berupa angka.',
            'is_active.required' => 'Status tidak boleh kosong.',
        ]);

        DB::beginTransaction();
        try {
            $tax->update($validated);
            DB::commit();

            return redirect()->route('master-tax.index')->with('success', 'Pajak berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gagal memperbarui pajak [ID: ' . $tax->id . ']: ' . $th->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }
}
