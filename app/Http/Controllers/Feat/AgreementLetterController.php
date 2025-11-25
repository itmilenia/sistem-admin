<?php

namespace App\Http\Controllers\Feat;

use Throwable;
use App\Models\AgreementLetter;
use App\Models\CustomerNetwork;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreAgreementLetterRequest;
use App\Http\Requests\UpdateAgreementLetterRequest;

class AgreementLetterController extends Controller
{
    private function authorizeLetterAction(AgreementLetter $letter, string $action)
    {
        $user = auth()->user();

        if ($letter->company_type === 'PT Milenia Mega Mandiri' && !$user->can("{$action}_surat_agreement_milenia")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Surat Agreement Milenia.');
        }

        if ($letter->company_type === 'PT Mega Auto Prima' && !$user->can("{$action}_surat_agreement_map")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Surat Agreement Map.');
        }
    }

    public function landing()
    {
        return view('pages.feat.sales.agreement-letter.landing');
    }

    public function indexMilenia()
    {
        $agreementLetters = AgreementLetter::with('customer')
            ->where('company_type', 'PT Milenia Mega Mandiri')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.feat.sales.agreement-letter.milenia.index', compact('agreementLetters'));
    }

    public function showMilenia($id)
    {
        $agreementLetter = AgreementLetter::with('customer')->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($agreementLetter, 'lihat');

        return view('pages.feat.sales.agreement-letter.milenia.detail', compact('agreementLetter'));
    }

    public function createMilenia()
    {
        $customers = CustomerNetwork::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        $salesNames = User::role(['trainer_milenia', 'sales_milenia', 'head_sales_milenia'])
            ->where('Aktif', 1)
            ->pluck('Nama');

        return view('pages.feat.sales.agreement-letter.milenia.create', compact('customers', 'salesNames'));
    }

    public function editMilenia($id)
    {
        $agreementLetter = AgreementLetter::with('customer')->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($agreementLetter, 'ubah');

        $customers = CustomerNetwork::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        $salesNames = User::role(['trainer_milenia', 'sales_milenia', 'head_sales_milenia'])
            ->where('Aktif', 1)
            ->pluck('Nama');

        return view('pages.feat.sales.agreement-letter.milenia.edit', compact('agreementLetter', 'customers', 'salesNames'));
    }

    public function indexMap()
    {
        $agreementLetters = AgreementLetter::with('customer')
            ->where('company_type', 'PT Mega Auto Prima')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.feat.sales.agreement-letter.map.index', compact('agreementLetters'));
    }

    public function showMap($id)
    {
        $agreementLetter = AgreementLetter::with('customer')->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($agreementLetter, 'lihat');

        return view('pages.feat.sales.agreement-letter.map.detail', compact('agreementLetter'));
    }

    public function createMap()
    {
        $customers = CustomerNetwork::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        $salesNames = User::role(['trainer_map', 'sales_map', 'head_sales_map'])
            ->where('Aktif', 1)
            ->pluck('Nama');

        return view('pages.feat.sales.agreement-letter.map.create', compact('customers', 'salesNames'));
    }

    public function editMap($id)
    {
        $agreementLetter = AgreementLetter::with('customer')->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($agreementLetter, 'ubah');

        $customers = CustomerNetwork::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        $salesNames = User::role(['trainer_map', 'sales_map', 'head_sales_map'])
            ->where('Aktif', 1)
            ->pluck('Nama');

        return view('pages.feat.sales.agreement-letter.map.edit', compact('agreementLetter', 'customers', 'salesNames'));
    }

    public function store(StoreAgreementLetterRequest $request)
    {
        $validated = $request->validated();

        $user = auth()->user();

        // 1. Cek permission berdasarkan company_type (mengadaptasi dari referensi)
        if ($validated['company_type'] === 'PT Milenia Mega Mandiri' && !$user->can('buat_surat_agreement_milenia')) {
            abort(403, 'Anda tidak memiliki izin membuat Surat Agreement Milenia.');
        }

        if ($validated['company_type'] === 'PT Mega Auto Prima' && !$user->can('buat_surat_agreement_map')) {
            abort(403, 'Anda tidak memiliki izin membuat Surat Agreement MAP.');
        }

        // Ambil ID user, sesuaikan dengan primary key 'ID' di tabel users Anda
        $userId = $user->ID;

        $file = $request->file('agreement_letter_file');
        $filePath = null;

        DB::beginTransaction();

        try {
            // 2. Penyimpanan File
            if ($file) {
                $fileExtension = $file->getClientOriginalExtension();

                // Membuat nama file yang unik
                $fileName = 'SuratAgreement-' . $validated['customer_id'] . '-' . date('YmdHis') . '.' . $fileExtension;

                $folderPath = 'agreement_letters';
                $filePath = $file->storeAs($folderPath, $fileName, 'public');

                if (!$filePath) {
                    throw new \Exception('Gagal menyimpan file surat ke storage.');
                }
            }

            // 3. Penyimpanan Data Model
            $agreementLetter = AgreementLetter::create([
                'customer_id' => $validated['customer_id'],
                'company_type' => $validated['company_type'],
                'sales_name' => $validated['sales_name'],
                'effective_start_date' => $validated['effective_start_date'],
                'effective_end_date' => $validated['effective_end_date'],
                'agreement_letter_path' => $filePath,
                'is_active' => $validated['is_active'],
                'created_by' => $userId,
                'updated_by' => null,
            ]);

            if (!$agreementLetter) {
                throw new \Exception('Gagal menyimpan data surat agreement.');
            }

            // 4. Commit Transaksi
            DB::commit();

            // 5. Redirect ke halaman index sesuai company_type
            if ($validated['company_type'] === 'PT Milenia Mega Mandiri') {
                return redirect()->route('agreement-letter.milenia.index')
                    ->with('success', 'Surat Agreement Milenia baru berhasil ditambahkan.');
            } elseif ($validated['company_type'] === 'PT Mega Auto Prima') {
                return redirect()->route('agreement-letter.map.index')
                    ->with('success', 'Surat Agreement MAP baru berhasil ditambahkan.');
            }
        } catch (Throwable $e) {
            // 6. Rollback Transaksi jika terjadi kesalahan
            DB::rollBack();

            // 7. Hapus File yang terlanjur tersimpan
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Log error untuk debugging
            Log::error('Gagal menyimpan Agreement Letter: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId ?? null,
                'input' => $request->except('agreement_letter_file'),
            ]);

            // Redirect ke halaman sebelumnya dengan pesan error
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi. Detail: ' . $e->getMessage());
        }
    }

    public function update(UpdateAgreementLetterRequest $request, $id)
    {
        $agreementLetter = AgreementLetter::findOrFail($id);

        $validated = $request->validated();
        $user = auth()->user();

        // 1. Cek permission
        // Sesuaikan nama permission jika perlu
        if ($validated['company_type'] === 'PT Milenia Mega Mandiri' && !$user->can('ubah_surat_agreement_milenia')) {
            abort(403, 'Anda tidak memiliki izin mengubah Surat Agreement Milenia.');
        }

        if ($validated['company_type'] === 'PT Mega Auto Prima' && !$user->can('ubah_surat_agreement_map')) {
            abort(403, 'Anda tidak memiliki izin mengubah Surat Agreement MAP.');
        }

        $userId = $user->ID;
        $file = $request->file('agreement_letter_file');
        $newFilePath = null;
        $oldFilePath = $agreementLetter->agreement_letter_path;

        DB::beginTransaction();

        try {
            // 2. Penyimpanan File Baru
            if ($file) {
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = 'SuratAgreement-' . $validated['customer_id'] . '-' . date('YmdHis') . '.' . $fileExtension;

                $folderPath = 'agreement_letters';
                $newFilePath = $file->storeAs($folderPath, $fileName, 'public');

                if (!$newFilePath) {
                    throw new \Exception('Gagal menyimpan file surat baru ke storage.');
                }
            }

            // 3. Persiapan Data Update
            $updateData = $validated;
            $updateData['updated_by'] = $userId;

            if ($newFilePath) {
                $updateData['agreement_letter_path'] = $newFilePath;
            }

            // 4. Update Data Model
            $agreementLetter->update($updateData);

            // 5. Hapus File Lama
            if ($newFilePath && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }

            // 6. Commit Transaksi
            DB::commit();

            // 7. Redirect ke halaman index sesuai company_type
            if ($validated['company_type'] === 'PT Milenia Mega Mandiri') {
                return redirect()->route('agreement-letter.milenia.index')
                    ->with('success', 'Surat Agreement Milenia berhasil diperbarui.');
            } elseif ($validated['company_type'] === 'PT Mega Auto Prima') {
                return redirect()->route('agreement-letter.map.index')
                    ->with('success', 'Surat Agreement MAP berhasil diperbarui.');
            }
        } catch (Throwable $e) {
            // 8. Rollback Transaksi
            DB::rollBack();

            // 9. Hapus File Baru yang terlanjur tersimpan
            if ($newFilePath && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }

            // Log error
            Log::error('Gagal meng-update Agreement Letter: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId ?? null,
                'agreement_id' => $agreementLetter->id,
                'input' => $request->except('agreement_letter_file'),
            ]);

            // Redirect ke halaman sebelumnya dengan pesan error
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi. Detail: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $agreementLetter = AgreementLetter::findOrFail($id);
        $company_type = $agreementLetter->company_type;
        $user = auth()->user();

        // 1. Cek permission berdasarkan company_type
        if ($company_type === 'PT Milenia Mega Mandiri' && !$user->can('hapus_surat_agreement_milenia')) {
            abort(403, 'Anda tidak memiliki izin menghapus Surat Agreement Milenia.');
        }

        if ($company_type === 'PT Mega Auto Prima' && !$user->can('hapus_surat_agreement_map')) {
            abort(403, 'Anda tidak memiliki izin menghapus Surat Agreement MAP.');
        }

        $filePath = $agreementLetter->agreement_letter_path;

        DB::beginTransaction();

        try {
            // 2. Hapus data dari database
            $agreementLetter->delete();

            // 3. Hapus file dari storage
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // 4. Commit Transaksi
            DB::commit();

            // redirect ke halaman index sesuai company_type
            if ($company_type === 'PT Milenia Mega Mandiri') {
                return redirect()->route('agreement-letter.milenia.index')
                    ->with('success', 'Surat Agreement Milenia berhasil dihapus.');
            } elseif ($company_type === 'PT Mega Auto Prima') {
                return redirect()->route('agreement-letter.map.index')
                    ->with('success', 'Surat Agreement MAP berhasil dihapus.');
            }

            // 5. Redirect ke halaman index
            return redirect()->route('agreement-letter.milenia.index')
                ->with('success', 'Surat Agreement berhasil dihapus.');
        } catch (Throwable $e) {
            // 6. Rollback Transaksi jika terjadi error
            DB::rollBack();

            // Log error
            Log::error('Gagal menghapus Agreement Letter: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->ID ?? null,
                'agreement_id' => $agreementLetter->id,
            ]);

            // Redirect ke halaman sebelumnya dengan pesan error
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi. Detail: ' . $e->getMessage());
        }
    }
}
