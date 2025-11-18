<?php

namespace App\Http\Controllers\Feat;

use Exception;
use App\Models\User;
use App\Models\ItemMap;
use App\Models\ItemMilenia;
use App\Models\ClaimProduct;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ClaimProductDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\SignatureUploadTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreClaimProductRequest;

class ClaimProductFormController extends Controller
{
    use SignatureUploadTrait;
    use FileUploadTrait;

    private function authorizeClaimAction(ClaimProduct $claim, string $action)
    {
        $user = auth()->user();

        if ($claim->company_type === 'PT Milenia Mega Mandiri' && !$user->can("{$action}_klaim_produk_milenia")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Klaim Produk Milenia.');
        }

        if ($claim->company_type === 'PT Mega Auto Prima' && !$user->can("{$action}_klaim_produk_map")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Klaim Produk Map.');
        }
    }

    public function landing()
    {
        return view('pages.feat.promo-produk.claim-product-form.landing');
    }

    public function indexMilenia()
    {
        $claims = ClaimProduct::with('sales', 'salesHead', 'checker')
            ->where('company_type', 'PT Milenia Mega Mandiri')
            ->latest()
            ->get();

        return view('pages.feat.promo-produk.claim-product-form.milenia.index', compact('claims'));
    }

    public function showMilenia($id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'lihat');

        $productIds = $claim->claimDetails->pluck('product_id')->unique();

        $products = ItemMilenia::on('sqlsrv_wh')
            ->whereIn('MFIMA_ItemID', $productIds)
            ->get()
            ->keyBy('MFIMA_ItemID');

        $claim->load(
            'claimDetails',
            'sales',
            'salesHead',
            'checker',
            'createdBy',
            'updatedBy'
        );

        return view('pages.feat.promo-produk.claim-product-form.milenia.detail', compact('claim', 'products'));
    }

    public function createMilenia()
    {
        $salesUsers = User::role('sales_milenia')->orderBy('Nama')->get();
        $salesHeads = User::role('head_sales_milenia')->orderBy('Nama')->get();
        $checkers = User::role('trainer_milenia')->orderBy('Nama')->get();

        $products = ItemMilenia::where('MFIMA_Active', 1)
            ->orderBy('MFIMA_Description', 'asc')
            ->get();

        return view('pages.feat.promo-produk.claim-product-form.milenia.create', compact('salesUsers', 'salesHeads', 'checkers', 'products'));
    }

    public function editMilenia($id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'ubah');

        if ($claim->checker_signature_path && $claim->sales_signature_path && $claim->sales_head_signature_path) {
            return back()->with('warning', 'Klaim ini sudah Anda tandatangani sebelumnya.');
        }

        $claim->load('claimDetails');

        $salesUsers = User::role('sales_milenia')->orderBy('Nama')->get();
        $salesHeads = User::role('head_sales_milenia')->orderBy('Nama')->get();
        $checkers = User::role('trainer_milenia')->orderBy('Nama')->get();

        $products = ItemMilenia::where('MFIMA_Active', 1)
            ->orderBy('MFIMA_Description', 'asc')
            ->get();

        return view('pages.feat.promo-produk.claim-product-form.milenia.edit', compact('claim', 'salesUsers', 'salesHeads', 'checkers', 'products'));
    }

    public function indexMap()
    {
        $claims = ClaimProduct::with('sales', 'salesHead', 'checker')
            ->where('company_type', 'PT Mega Auto Prima')
            ->latest()
            ->get();

        return view('pages.feat.promo-produk.claim-product-form.map.index', compact('claims'));
    }

    public function showMap($id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'lihat');

        $productIds = $claim->claimDetails->pluck('product_id')->unique();

        $products = ItemMap::on('sqlsrv_snx')
            ->whereIn('MFIMA_ItemID', $productIds)
            ->get()
            ->keyBy('MFIMA_ItemID');

        $claim->load(
            'claimDetails',
            'sales',
            'salesHead',
            'checker',
            'createdBy',
            'updatedBy'
        );

        return view('pages.feat.promo-produk.claim-product-form.map.detail', compact('claim', 'products'));
    }

    public function createMap()
    {
        $salesUsers = User::role('sales_map')->orderBy('Nama')->get();
        $salesHeads = User::role('head_sales_map')->orderBy('Nama')->get();
        $checkers = User::role('trainer_map')->orderBy('Nama')->get();

        $products = ItemMap::where('MFIMA_Active', 1)
            ->orderBy('MFIMA_Description', 'asc')
            ->get();

        return view('pages.feat.promo-produk.claim-product-form.map.create', compact('salesUsers', 'salesHeads', 'checkers', 'products'));
    }

    public function editMap($id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'ubah');

        if ($claim->checker_signature_path && $claim->sales_signature_path && $claim->sales_head_signature_path) {
            return back()->with('warning', 'Klaim ini sudah Anda tandatangani sebelumnya.');
        }

        $claim->load('claimDetails');

        $salesUsers = User::role('sales_map')->orderBy('Nama')->get();
        $salesHeads = User::role('head_sales_map')->orderBy('Nama')->get();
        $checkers = User::role('trainer_map')->orderBy('Nama')->get();

        $products = ItemMap::where('MFIMA_Active', 1)
            ->orderBy('MFIMA_Description', 'asc')
            ->get();

        return view('pages.feat.promo-produk.claim-product-form.map.edit', compact('claim', 'salesUsers', 'salesHeads', 'checkers', 'products'));
    }

    public function store(StoreClaimProductRequest $request)
    {
        // Mulai database transaction
        DB::beginTransaction();
        $user = auth()->user();

        if ($request->company_type === 'PT Milenia Mega Mandiri' && !$user->can('buat_klaim_produk_milenia')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah klaim produk milenia.');
        } else if ($request->company_type === 'PT Mega Auto Prima' && !$user->can('buat_klaim_produk_map')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah klaim produk map.');
        }

        try {
            $headerData = $request->only([
                'company_type',
                'sales_id',
                'sales_head_id',
                'checker_id',
                'retail_name',
                'claim_date',
            ]);

            $headerData['created_by'] = Auth::id();
            $claimHeader = ClaimProduct::create($headerData);

            $detailsData = [];
            foreach ($request->details as $index => $detail) {

                $detailRowData = $detail;
                $imagePath = null;

                if ($request->hasFile("details.{$index}.product_image")) {
                    $file = $request->file("details.{$index}.product_image");

                    $imagePath = $this->uploadFile($file, 'claim_images', 'public');

                    if ($imagePath === false) {
                        throw new Exception("Gagal mengunggah gambar untuk baris " . ($index + 1));
                    }
                }
                $detailRowData['product_image'] = $imagePath;

                $detailsData[] = new ClaimProductDetail($detailRowData);
            }

            if (!empty($detailsData)) {
                $claimHeader->claimDetails()->saveMany($detailsData);
            }

            DB::commit();

            if ($claimHeader->company_type === 'PT Milenia Mega Mandiri') {
                return redirect()->route('product-claim-form.milenia.index')->with('success', 'Klaim Produk berhasil disimpan.');
            } else {
                return redirect()->route('product-claim-form.map.index')->with('success', 'Klaim Produk berhasil disimpan.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan Klaim Produk: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi. Pesan: ' . $e->getMessage());
        }
    }

    public function update(StoreClaimProductRequest $request, $id)
    {
        $claim = ClaimProduct::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeClaimAction($claim, 'ubah');

        // jika sudah ada tanda tangan checker, sales, sales_head maka tidak bisa diupdate
        if ($claim->checker_signature_path && $claim->sales_signature_path && $claim->sales_head_signature_path) {
            return back()->with('warning', 'Klaim ini sudah Anda tandatangani sebelumnya.');
        }

        // Ambil koleksi detail LAMA sebelum diapa-apakan
        $oldDetails = $claim->claimDetails;
        $oldImagePaths = $oldDetails->pluck('product_image')->filter();

        DB::beginTransaction();
        try {
            $headerData = $request->only([
                'company_type',
                'sales_id',
                'sales_head_id',
                'checker_id',
                'retail_name',
                'claim_date',
            ]);

            $headerData['updated_by'] = Auth::id();
            $claim->update($headerData);

            $claim->claimDetails()->delete();

            $newDetailsData = [];
            $newImagePaths = [];

            foreach ($request->details as $index => $detailInput) {

                $imagePath = null;
                $oldImagePath = $detailInput['old_product_image'] ?? null;

                if ($request->hasFile("details.{$index}.product_image")) {
                    $file = $request->file("details.{$index}.product_image");

                    $imagePath = $this->uploadFile($file, 'claim_images', 'public');
                    if ($imagePath === false) {
                        throw new Exception("Gagal mengunggah gambar baru untuk baris " . ($index + 1));
                    }
                } else if (!empty($oldImagePath)) {
                    $imagePath = $oldImagePath;
                }

                $detailInput['product_image'] = $imagePath;
                $newDetailsData[] = new ClaimProductDetail($detailInput);

                if ($imagePath) {
                    $newImagePaths[] = $imagePath;
                }
            }

            if (!empty($newDetailsData)) {
                $claim->claimDetails()->saveMany($newDetailsData);
            }

            $pathsToDelete = $oldImagePaths->diff($newImagePaths);
            foreach ($pathsToDelete as $path) {
                $this->deleteFile($path, 'public');
            }

            DB::commit();

            if ($claim->company_type === 'PT Milenia Mega Mandiri') {
                return redirect()->route('product-claim-form.milenia.index')->with('success', 'Klaim Produk berhasil diperbarui.');
            } else {
                return redirect()->route('product-claim-form.map.index')->with('success', 'Klaim Produk berhasil diperbarui.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal update Klaim Produk: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data. Pesan: ' . $e->getMessage());
        }
    }

    public function showVerifyForm($id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'verifikasi');

        if ($claim->verification_date) {
            return redirect()->route('claims.show', $claim->id)
                ->with('warning', 'Klaim ini sudah diverifikasi.');
        }

        $productIds = $claim->claimDetails->pluck('product_id')->unique();
        $products = ItemMilenia::on('sqlsrv_wh')
            ->whereIn('MFIMA_ItemID', $productIds)
            ->get()
            ->keyBy('MFIMA_ItemID');

        $claim->load('claimDetails', 'sales', 'salesHead', 'checker');

        return view('pages.feat.promo-produk.claim-product-form.verify-checker', compact('claim', 'products'));
    }

    public function storeVerification(Request $request, $id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'verifikasi');

        // 1. Validasi input
        $validated = $request->validate([
            'verification_date' => 'required|date',
            'verification_result' => 'required|string|max:2000',
            'checker_signature' => 'required|string',
        ], [
            'checker_signature.required' => 'Tanda tangan checker wajib diisi.'
        ]);

        // 2. Cek apakah sudah terverifikasi
        if ($claim->verification_date) {
            if ($claim->company_type == 'PT Milenia Mega Mandiri') {
                return redirect()->route('product-claim-form.milenia.show', $claim->id)
                    ->with('warning', 'Klaim ini sudah diverifikasi sebelumnya.');
            } else {
                return redirect()->route('product-claim-form.map.show', $claim->id)
                    ->with('warning', 'Klaim ini sudah diverifikasi sebelumnya.');
            }
        }

        DB::beginTransaction();
        try {
            $filename = 'checker_ttd_' . $claim->id . '_' . time() . '.png';
            $relativePath = 'signatures/claims/' . $filename;

            $this->saveSignature($validated['checker_signature'], $relativePath);

            $claim->verification_date = $validated['verification_date'];
            $claim->verification_result = $validated['verification_result'];
            $claim->checker_signature_path = $relativePath;
            $claim->save();

            DB::commit();

            if ($claim->company_type == 'PT Milenia Mega Mandiri') {
                return redirect()->route('product-claim-form.milenia.index', $claim->id)
                    ->with('success', 'Klaim berhasil diverifikasi.');
            } else {
                return redirect()->route('product-claim-form.map.index', $claim->id)
                    ->with('success', 'Klaim berhasil diverifikasi.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan verifikasi klaim: ' . $e->getMessage());

            if (isset($path) && Storage::exists($path)) {
                Storage::delete($path);
            }

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan verifikasi. ' . $e->getMessage());
        }
    }

    public function showSalesSignature($id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'tanda_tangan_sales');

        // 1. Validasi Keamanan: Hanya sales_id yang berhak
        if (Auth::id() != $claim->sales_id && !Auth::user()->hasRole('owner')) {
            abort(403, 'Anda tidak memiliki wewenang untuk menandatangani klaim ini.');
        }

        // 2. Validasi Alur: Pastikan checker sudah verifikasi
        if (!$claim->verification_date || !$claim->checker_signature_path) {
            // Tentukan redirect berdasarkan company_type
            $route = $this->getRedirectRoute($claim, 'show');
            return redirect()->route($route, $claim->id)
                ->with('error', 'Klaim ini harus diverifikasi oleh Checker terlebih dahulu.');
        }

        // 3. Validasi Alur: Cek apakah sales sudah TTD
        if ($claim->sales_signature_path) {
            $route = $this->getRedirectRoute($claim, 'show');
            return redirect()->route($route, $claim->id)
                ->with('warning', 'Klaim ini sudah Anda tandatangani sebelumnya.');
        }

        // Ambil data produk
        $productIds = $claim->claimDetails->pluck('product_id')->unique();
        $products = ItemMilenia::on('sqlsrv_wh')
            ->whereIn('MFIMA_ItemID', $productIds)
            ->get()
            ->keyBy('MFIMA_ItemID');

        $claim->load('claimDetails', 'sales', 'salesHead', 'checker');

        // Menggunakan view baru
        return view('pages.feat.promo-produk.claim-product-form.verify-sales', compact('claim', 'products'));
    }

    public function storeSalesSignature(Request $request, $id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'tanda_tangan_sales');

        // 1. Validasi Keamanan: Hanya sales_id yang berhak
        if (Auth::id() != $claim->sales_id && !Auth::user()->hasRole('owner')) {
            abort(403, 'Anda tidak memiliki wewenang untuk menyimpan tanda tangan ini.');
        }

        // 2. Validasi Alur: Cek prerequisite dan apakah sudah TTD
        if (!$claim->verification_date) {
            return back()->with('error', 'Klaim ini belum diverifikasi oleh Checker.');
        }
        if ($claim->sales_signature_path) {
            return back()->with('warning', 'Klaim ini sudah Anda tandatangani sebelumnya.');
        }

        // 3. Validasi Input: Hanya perlu TTD
        $validated = $request->validate([
            'sales_signature' => 'required|string',
        ], [
            'sales_signature.required' => 'Tanda tangan sales wajib diisi.'
        ]);

        $relativePath = null;
        DB::beginTransaction();
        try {
            // 4. Tentukan nama file dan path
            $filename = 'sales_ttd_' . $claim->id . '_' . time() . '.png';
            $relativePath = 'signatures/claims/' . $filename;

            // 5. Panggil helper Trait untuk menyimpan TTD
            $this->saveSignature($validated['sales_signature'], $relativePath);

            // 6. Update model (hanya path TTD)
            $claim->sales_signature_path = $relativePath;
            $claim->save();

            DB::commit();

            // 7. Redirect (Asumsi kembali ke index)
            $route = $this->getRedirectRoute($claim, 'index');
            return redirect()->route($route) // Pergi ke index, bukan show
                ->with('success', 'Tanda tangan Sales berhasil disimpan.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan TTD Sales: ' . $e->getMessage());

            // Hapus file jika terlanjur tersimpan
            if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan TTD. ' . $e->getMessage());
        }
    }

    public function showSalesHeadSignature($id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'tanda_tangan_head_sales');

        // 1. Validasi Keamanan: Hanya sales_head_id yang berhak
        if (Auth::id() != $claim->sales_head_id && !Auth::user()->hasRole('owner')) {
            abort(403, 'Anda tidak memiliki wewenang untuk menandatangani klaim ini.');
        }

        // 2. Validasi Alur: Pastikan checker sudah verifikasi
        if (!$claim->verification_date || !$claim->checker_signature_path) {
            $route = $this->getRedirectRoute($claim, 'show');
            return redirect()->route($route, $claim->id)
                ->with('error', 'Klaim ini harus diverifikasi oleh Checker terlebih dahulu.');
        }

        // 3. Validasi Alur: Pastikan sales sudah TTD
        if (!$claim->sales_signature_path) {
            $route = $this->getRedirectRoute($claim, 'show');
            return redirect()->route($route, $claim->id)
                ->with('error', 'Klaim ini harus ditandatangani oleh Sales terlebih dahulu.');
        }

        // 4. Validasi Alur: Cek apakah sales head sudah TTD
        if ($claim->sales_head_signature_path) {
            $route = $this->getRedirectRoute($claim, 'show');
            return redirect()->route($route, $claim->id)
                ->with('warning', 'Klaim ini sudah Anda (Sales Head) tandatangani sebelumnya.');
        }

        // Ambil data produk (sama seperti di show/verify)
        $productIds = $claim->claimDetails->pluck('product_id')->unique();
        $products = ItemMilenia::on('sqlsrv_wh') // Ganti ItemMilenia jika perlu
            ->whereIn('MFIMA_ItemID', $productIds)
            ->get()
            ->keyBy('MFIMA_ItemID');

        $claim->load('claimDetails', 'sales', 'salesHead', 'checker');

        // Menggunakan view baru: 'verify-sales-head'
        return view('pages.feat.promo-produk.claim-product-form.verify-sales-head', compact('claim', 'products'));
    }

    public function storeSalesHeadSignature(Request $request, $id)
    {
        $claim = ClaimProduct::findOrFail($id);

        $this->authorizeClaimAction($claim, 'tanda_tangan_head_sales');

        // 1. Validasi Keamanan: Hanya sales_head_id yang berhak
        if (Auth::id() != $claim->sales_head_id && !Auth::user()->hasRole('owner')) {
            abort(403, 'Anda tidak memiliki wewenang untuk menyimpan tanda tangan ini.');
        }

        // 2. Validasi Alur: Cek semua prasyarat
        if (!$claim->verification_date || !$claim->checker_signature_path) {
            return back()->with('error', 'Klaim ini belum diverifikasi oleh Checker.');
        }
        if (!$claim->sales_signature_path) {
            return back()->with('error', 'Klaim ini belum ditandatangani oleh Sales.');
        }
        if ($claim->sales_head_signature_path) {
            return back()->with('warning', 'Klaim ini sudah Anda (Sales Head) tandatangani sebelumnya.');
        }

        // 3. Validasi Input: Hanya perlu TTD
        $validated = $request->validate([
            'sales_head_signature' => 'required|string',
        ], [
            'sales_head_signature.required' => 'Tanda tangan Sales Head wajib diisi.'
        ]);

        $relativePath = null;
        DB::beginTransaction();
        try {
            // 4. Tentukan nama file dan path
            $filename = 'sales_head_ttd_' . $claim->id . '_' . time() . '.png';
            $relativePath = 'signatures/claims/' . $filename;

            // 5. Panggil helper Trait untuk menyimpan TTD
            $this->saveSignature($validated['sales_head_signature'], $relativePath);

            // 6. Update model
            $claim->sales_head_signature_path = $relativePath;
            $claim->save();

            DB::commit();

            // 7. Redirect (Kembali ke index)
            $route = $this->getRedirectRoute($claim, 'index');
            return redirect()->route($route)
                ->with('success', 'Tanda tangan Sales Head berhasil disimpan. Proses klaim selesai.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan TTD Sales Head: ' . $e->getMessage());

            // Hapus file jika terlanjur tersimpan
            if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan TTD. ' . $e->getMessage());
        }
    }

    public function exportPDF($id)
    {
        $claim = ClaimProduct::with('claimDetails', 'sales', 'salesHead', 'checker')->findOrFail($id);

        $this->authorizeClaimAction($claim, 'buat');

        // Ambil data produk (sama seperti di method show)
        $productIds = $claim->claimDetails->pluck('product_id')->unique();

        // Sesuaikan query ini dengan logic Anda jika ada Tipe MAP
        $products = ItemMilenia::on('sqlsrv_wh')
            ->whereIn('MFIMA_ItemID', $productIds)
            ->get()
            ->keyBy('MFIMA_ItemID');

        // DOMPDF memerlukan path absolut ke file, bukan URL
        // Kita gunakan storage_path() untuk ini.
        $checkerSignaturePath = null;
        if ($claim->checker_signature_path && Storage::disk('public')->exists($claim->checker_signature_path)) {
            $checkerSignaturePath = storage_path('app/public/' . $claim->checker_signature_path);
        }

        $salesSignaturePath = null;
        if ($claim->sales_signature_path && Storage::disk('public')->exists($claim->sales_signature_path)) {
            $salesSignaturePath = storage_path('app/public/' . $claim->sales_signature_path);
        }

        $salesHeadSignaturePath = null;
        if ($claim->sales_head_signature_path && Storage::disk('public')->exists($claim->sales_head_signature_path)) {
            $salesHeadSignaturePath = storage_path('app/public/' . $claim->sales_head_signature_path);
        }

        $data = [
            'claim' => $claim,
            'products' => $products,
            'checkerSignaturePath' => $checkerSignaturePath,
            'salesSignaturePath' => $salesSignaturePath,
            'salesHeadSignaturePath' => $salesHeadSignaturePath
        ];

        $pdf = Pdf::loadView('pages.feat.promo-produk.claim-product-form.pdf-template', $data);

        // Atur nama file
        $fileName = 'FORM-KLAIM-' . $claim->id . '-' . $claim->retail_name . '.pdf';

        // Tampilkan di browser
        return $pdf->stream($fileName);
    }

    public function destroy($id)
    {
        $claim = ClaimProduct::findOrFail($id);
        $claimDetail = ClaimProductDetail::where('product_claim_id', $claim->id)->first();

        $this->authorizeClaimAction($claim, 'hapus');

        if ($claim->sales_head_signature_path) {
            $redirectRoute = $this->getRedirectRoute($claim, 'show');
            return redirect()->route($redirectRoute, $claim->id)
                ->with('error', 'Klaim yang sudah selesai ditandatangani tidak dapat dihapus.');
        }

        $pathsToDelete = [
            $claim->checker_signature_path,
            $claim->sales_signature_path,
            $claim->sales_head_signature_path,
            $claimDetail->product_image
        ];

        // Ambil SEMUA path gambar dari relasi claimDetails
        $detailImagePaths = $claim->claimDetails->pluck('product_image')->filter();

        // Gabungkan semua path (header & detail) menjadi satu array
        $allPaths = $detailImagePaths->merge($pathsToDelete)->all();

        $redirectRoute = $this->getRedirectRoute($claim, 'index');

        DB::beginTransaction();
        try {
            // 1. Hapus detail (child)
            $claim->claimDetails()->delete();

            // 2. Hapus header (parent)
            $claim->delete();

            // 3. Commit transaksi database
            DB::commit();

            // 4. Hapus file dari storage SETELAH database berhasil
            foreach ($allPaths as $path) {
                $this->deleteFile($path, 'public');
            }

            return redirect()->route($redirectRoute)
                ->with('success', 'Klaim berhasil dihapus secara permanen.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus Klaim Produk: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus data. ' . $e->getMessage());
        }
    }

    private function getRedirectRoute($claim, $action = 'index')
    {
        $baseRouteName = $claim->company_type == 'PT Milenia Mega Mandiri'
            ? 'product-claim-form.milenia'
            : 'product-claim-form.map';

        return $baseRouteName . '.' . $action;
    }
}
