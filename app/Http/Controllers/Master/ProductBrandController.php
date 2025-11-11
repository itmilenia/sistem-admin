<?php

namespace App\Http\Controllers\Master;

use App\Models\ProductBrand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProductBrandRequest;
use App\Http\Requests\UpdateProductBrandRequest;

class ProductBrandController extends Controller
{
    public function index()
    {
        $productBrands = ProductBrand::orderBy('brand_name', 'asc')->get();

        return view('pages.master.product-brand.index', compact('productBrands'));
    }

    public function create()
    {
        return view('pages.master.product-brand.create');
    }

    public function store(StoreProductBrandRequest $request)
    {
        // 1. Data Tervalidasi
        $validatedData = $request->validated();

        // 2. Ambil koneksi dan tabel langsung dari model
        $connectionName = (new ProductBrand())->getConnectionName();

        try {
            // 3. Mulai Database Transaction
            DB::connection($connectionName)->transaction(function () use ($validatedData) {
                // 4. Siapkan data untuk disimpan
                $dataToCreate = $validatedData;

                // 5. Tambahkan user stamp
                $dataToCreate['created_by'] = Auth::id();
                $dataToCreate['updated_by'] = Auth::id();

                // 6. Buat record baru
                ProductBrand::create($dataToCreate);
            });
            // 7. Jika transaksi sukses
            return redirect()->route('master-product-brand.index')
                ->with('success', 'Product Brand baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            // 8. Rollback transaksi
            DB::connection($connectionName)->rollBack();

            // 9. Jika terjadi error (transaksi otomatis di-rollback)
            Log::error('Gagal menyimpan Product Brand: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            // 10. Redirect kembali ke form dengan error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function edit($id)
    {
        $productBrand = ProductBrand::findOrFail($id);
        return view('pages.master.product-brand.edit', compact('productBrand'));
    }

    public function update(UpdateProductBrandRequest $request, $id)
    {
        // 1. Ambil data yang sudah tervalidasi
        $validatedData = $request->validated();

        // 2. Cari brand yang akan di-update
        $productBrand = ProductBrand::findOrFail($id);

        // 3. Ambil nama koneksi dari model
        $connectionName = $productBrand->getConnectionName();

        try {
            // 4. Mulai Database Transaction
            DB::connection($connectionName)->transaction(function () use ($productBrand, $validatedData) {

                // 5. Siapkan data untuk di-update
                $dataToUpdate = $validatedData;

                // 6. Tambahkan user stamp
                $dataToUpdate['updated_by'] = Auth::id();

                // 7. Update record
                $productBrand->update($dataToUpdate);
            });

            // 8. Jika transaksi sukses
            return redirect()->route('master-product-brand.index')
                ->with('success', 'Product Brand berhasil diperbarui.');
        } catch (\Exception $e) {

            // 9. Rollback transaksi
            DB::connection($connectionName)->rollBack();

            // 10. Jika terjadi error (transaksi otomatis di-rollback)
            Log::error('Gagal memperbarui Product Brand: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            // 11. Redirect kembali ke form edit
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }
}
