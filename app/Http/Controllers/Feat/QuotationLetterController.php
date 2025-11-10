<?php

namespace App\Http\Controllers\Feat;

use Throwable;
use Illuminate\Http\Request;
use App\Models\QuotationLetter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreQuotationLetterRequest;
use App\Http\Requests\UpdateQuotationLetterRequest;

class QuotationLetterController extends Controller
{
    private function authorizeLetterAction(QuotationLetter $letter, string $action)
    {
        $user = auth()->user();

        if ($letter->letter_type === 'Milenia' && !$user->can("{$action}_surat_penawaran_milenia")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Surat Penawaran Milenia.');
        }

        if ($letter->letter_type === 'Map' && !$user->can("{$action}_surat_penawaran_map")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Surat Penawaran Map.');
        }
    }

    public function landing()
    {
        return view('pages.feat.sales.quotation-letter.landing');
    }

    public function indexMilenia()
    {
        $quotationLetters = QuotationLetter::where('letter_type', 'Milenia')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.feat.sales.quotation-letter.milenia.index', compact('quotationLetters'));
    }

    public function showMilenia($id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'lihat');

        return view('pages.feat.sales.quotation-letter.milenia.detail', compact('quotationLetter'));
    }

    public function createMilenia()
    {
        return view('pages.feat.sales.quotation-letter.milenia.create');
    }

    public function editMilenia($id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'ubah');

        return view('pages.feat.sales.quotation-letter.milenia.edit', compact('quotationLetter'));
    }

    public function indexMap()
    {
        $quotationLetters = QuotationLetter::where('letter_type', 'Map')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.feat.sales.quotation-letter.map.index', compact('quotationLetters'));
    }

    public function showMap($id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'lihat');

        return view('pages.feat.sales.quotation-letter.map.detail', compact('quotationLetter'));
    }

    public function createMap()
    {
        return view('pages.feat.sales.quotation-letter.map.create');
    }

    public function editMap($id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'ubah');

        return view('pages.feat.sales.quotation-letter.map.edit', compact('quotationLetter'));
    }

    public function store(StoreQuotationLetterRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        // Cek permission sesuai tipe
        if ($validated['letter_type'] === 'Milenia' && !$user->can('buat_surat_penawaran_milenia')) {
            abort(403, 'Anda tidak memiliki izin membuat Surat Penawaran Milenia.');
        }

        if ($validated['letter_type'] === 'Map' && !$user->can('buat_surat_penawaran_map')) {
            abort(403, 'Anda tidak memiliki izin membuat Surat Penawaran Map.');
        }

        $userId = Auth::id() ?? 1;

        $file = $request->file('quotation_letter_file');
        $fileName = null;
        $filePath = null;

        DB::beginTransaction();

        try {
            // 1. Penyimpanan File
            if ($file) {
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = 'SuratPenawaran-' . $validated['quotation_letter_number'] . '-' . date('Ymd') . '.' . $fileExtension;

                $folderPath = 'quotation_letters';
                $filePath = $file->storeAs($folderPath, $fileName, 'public');

                if (!$filePath) {
                    throw new \Exception('Gagal menyimpan file surat ke storage.');
                }
            }

            // 2. Penyimpanan Data Model
            $quotationLetter = QuotationLetter::create([
                'quotation_letter_number' => $validated['quotation_letter_number'],
                'recipient' => $validated['recipient'],
                'letter_date' => $validated['letter_date'],
                'subject' => $validated['subject'],
                'quotation_letter_file' => $filePath,
                'letter_status' => $validated['letter_status'] ?? 'Draft',
                'letter_type' => $validated['letter_type'],
                'created_by' => $userId,
                'updated_by' => null,
            ]);

            if (!$quotationLetter) {
                throw new \Exception('Gagal menyimpan data surat penawaran.');
            }

            // 3. Commit Transaksi
            DB::commit();

            // cek data apakah milenia atau map, dan redirect ke halaman yang sesuai
            if ($validated['letter_type'] === 'Milenia') {
                return redirect()->route('quotation-letter.milenia.index')
                    ->with('success', 'Surat Penawaran Milenia baru berhasil ditambahkan.');
            } elseif ($validated['letter_type'] === 'Map') {
                return redirect()->route('quotation-letter.map.index')
                    ->with('success', 'Surat Penawaran Map baru berhasil ditambahkan.');
            }
        } catch (Throwable $e) {
            // 4. Rollback Transaksi jika terjadi kesalahan
            DB::rollBack();

            // 5. Hapus File yang terlanjur tersimpan (jika ada)
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Log error untuk debugging
            Log::error('Gagal menyimpan Quotation Letter: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
                'input' => $request->all(),
            ]);

            // Redirect ke halaman sebelumnya dengan pesan error
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi. Detail Error: ' . $e->getMessage());
        }
    }

    public function update(UpdateQuotationLetterRequest $request, $id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        $validated = $request->validated();

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'ubah');


        $userId = Auth::id() ?? 1;
        $file = $request->file('quotation_letter_file');
        $oldFilePath = $quotationLetter->quotation_letter_file;
        $newFilePath = $oldFilePath;

        DB::beginTransaction();

        try {
            // 1. Penanganan File Baru
            if ($file) {
                if ($oldFilePath) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                // Simpan file baru
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = 'SuratPenawaran-' . $validated['quotation_letter_number'] . '-' . date('Ymd') . '.' . $fileExtension;
                $folderPath = 'quotation_letters';
                $newFilePath = $file->storeAs($folderPath, $fileName, 'public');

                if (!$newFilePath) {
                    throw new \Exception('Gagal menyimpan file surat baru ke storage.');
                }
            }

            // 2. Pembaruan Data Model
            $quotationLetter->update([
                'quotation_letter_number' => $validated['quotation_letter_number'],
                'recipient' => $validated['recipient'],
                'letter_date' => $validated['letter_date'],
                'subject' => $validated['subject'],
                'quotation_letter_file' => $newFilePath,
                'letter_status' => $validated['letter_status'] ?? $quotationLetter->letter_status,
                'letter_type' => $validated['letter_type'],
                'updated_by' => $userId,
            ]);

            // 3. Commit Transaksi
            DB::commit();

            // ambil redirect url berdasarkan letter type
            if ($validated['letter_type'] === 'Milenia') {
                $redirectUrl = 'quotation-letter.milenia.index';
            } elseif ($validated['letter_type'] === 'Map') {
                $redirectUrl = 'quotation-letter.map.index';
            }

            return redirect()
                ->route($redirectUrl)
                ->with('success', 'Surat Penawaran dengan nomor ' . $quotationLetter->quotation_letter_number . ' berhasil diperbarui.');
        } catch (Throwable $e) {
            DB::rollBack();

            // 4. Cleanup: Jika file baru sempat diupload tapi update DB gagal, hapus file baru tersebut.
            // newFilePath akan berbeda dari oldFilePath hanya jika file baru berhasil disimpan.
            if ($file && $newFilePath && ($newFilePath !== $oldFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }

            Log::error('Gagal memperbarui Quotation Letter: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
                'input' => $request->all(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi. Detail Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        $this->authorizeLetterAction($quotationLetter, 'hapus');

        $letterNumber = $quotationLetter->quotation_letter_number;
        $lettey_type = $quotationLetter->letter_type;
        $filePath = $quotationLetter->quotation_letter_file;

        DB::beginTransaction();

        try {
            // 1. Hapus data dari database
            $quotationLetter->delete();

            // 2. Hapus file dari storage (jika ada)
            if ($filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                } else {
                    // Log jika path ada di DB tapi file tidak ada di storage (optional)
                    Log::warning('File tidak ditemukan di storage saat menghapus Quotation Letter: ' . $filePath, [
                        'letter_id' => $quotationLetter->id,
                        'letter_number' => $letterNumber
                    ]);
                }
            }

            // 3. Commit Transaksi
            DB::commit();

            // 4. Redirect ke halaman yang sesuai
            if ($lettey_type === 'Milenia') {
                $redirectUrl = 'quotation-letter.milenia.index';
            } elseif ($lettey_type === 'Map') {
                $redirectUrl = 'quotation-letter.map.index';
            }

            return redirect()
                ->route($redirectUrl)
                ->with('success', 'Surat Penawaran dengan nomor ' . $letterNumber . ' dan file lampirannya berhasil dihapus.');
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Gagal menghapus Quotation Letter: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'letter_id' => $quotationLetter->id,
                'letter_number' => $letterNumber
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi. Detail Error: ' . $e->getMessage());
        }
    }
}
