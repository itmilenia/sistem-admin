<?php

namespace App\Http\Controllers\Master;

use App\Models\ProductBrand;
use App\Models\CustomerNetwork;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCustomerNetworkRequest;
use App\Http\Requests\UpdateCustomerNetworkRequest;

class CustomerNetworkController extends Controller
{
    public function index()
    {
        $customerNetworks = CustomerNetwork::orderBy('name', 'asc')->get();

        return view('pages.master.customer-network.index', compact('customerNetworks'));
    }

    public function detail($id)
    {
        $customerNetwork = CustomerNetwork::findOrFail($id);
        return view('pages.master.customer-network.detail', compact('customerNetwork'));
    }

    public function create()
    {
        $customerNetworks = CustomerNetwork::orderBy('name', 'asc')->get();

        $productBrands = ProductBrand::where('is_active', 1)
            ->orderBy('brand_name', 'asc')->get();

        return view('pages.master.customer-network.create', compact('customerNetworks', 'productBrands'));
    }

    public function store(StoreCustomerNetworkRequest $request)
    {
        // 1. Ambil data yang sudah tervalidasi
        $validatedData = $request->validated();

        // 2. Ambil nama koneksi dari model untuk memastikan transaksi berjalan di DB yang tepat
        $connectionName = (new CustomerNetwork())->getConnectionName();

        try {
            // 3. Mulai Database Transaction
            DB::connection($connectionName)->transaction(function () use ($validatedData) {

                // 4. Siapkan data untuk disimpan
                $dataToCreate = $validatedData;

                // 5. Konversi 'category' menjadi string
                $dataToCreate['category'] = implode(',', $validatedData['category']);

                // 6. Tambahkan user stamp (created_by & updated_by)
                $dataToCreate['created_by'] = Auth::id();
                $dataToCreate['updated_by'] = Auth::id();

                // 7. Buat record baru
                CustomerNetwork::create($dataToCreate);
            });

            // 8. Jika transaksi sukses, redirect ke halaman index dengan pesan sukses
            return redirect()->route('master-customer-network.index')
                ->with('success', 'Data Jaringan Customer baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            // 9. Jika terjadi error, rollback transaksi
            DB::connection($connectionName)->rollBack();

            // 10. Log error
            Log::error('Gagal menyimpan Jaringan Customer: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            // 11. Redirect kembali ke form (halaman create) dengan pesan error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function edit($id)
    {
        $customerNetwork = CustomerNetwork::findOrFail($id);
        $productBrands = ProductBrand::where('is_active', 1)
            ->orderBy('brand_name', 'asc')->get();

        return view('pages.master.customer-network.edit', compact('customerNetwork', 'productBrands'));
    }

    public function update(UpdateCustomerNetworkRequest $request, $id)
    {
        // 1. Ambil data yang sudah tervalidasi
        $validatedData = $request->validated();

        // 2. Ambil nama koneksi dari model untuk memastikan transaksi berjalan di DB yang tepat
        $connectionName = (new CustomerNetwork())->getConnectionName();

        try {
            // 3. Mulai Database Transaction
            DB::connection($connectionName)->transaction(function () use ($validatedData, $id) {

                // 4. Siapkan data untuk disimpan
                $dataToUpdate = $validatedData;

                // 5. Konversi 'category' menjadi string
                $dataToUpdate['category'] = implode(',', $validatedData['category']);

                // 6. Tambahkan user stamp (updated_by)
                $dataToUpdate['updated_by'] = Auth::id();

                // 7. Update record
                CustomerNetwork::where('id', $id)->update($dataToUpdate);
            });

            // 8. Jika transaksi sukses, redirect ke halaman index dengan pesan sukses
            return redirect()->route('master-customer-network.index')
                ->with('success', 'Data Jaringan Customer berhasil diperbarui.');
        } catch (\Exception $e) {
            // 9. Jika terjadi error, rollback transaksi
            DB::connection($connectionName)->rollBack();

            // 10. Log error
            Log::error('Gagal memperbarui Jaringan Customer: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            // 11. Redirect kembali ke form (halaman edit) dengan pesan error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }
}
