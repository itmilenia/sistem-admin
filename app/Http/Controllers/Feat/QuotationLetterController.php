<?php

namespace App\Http\Controllers\Feat;

use Throwable;
use App\Models\Tax;
use App\Models\User;
use App\Models\ItemMap;
use App\Models\ItemMilenia;
use Illuminate\Http\Request;
use App\Models\QuotationLetter;
use App\Traits\FileUploadTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\SignatureUploadTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreQuotationLetterRequest;
use App\Http\Requests\UpdateQuotationLetterRequest;

class QuotationLetterController extends Controller
{
    use FileUploadTrait, SignatureUploadTrait;

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
        $quotationLetter = QuotationLetter::with([
            'details.itemMilenia',
            'creator',
            'updater',
            'signer'
        ])
            ->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'lihat');

        return view('pages.feat.sales.quotation-letter.milenia.detail', compact('quotationLetter'));
    }

    public function createMilenia()
    {
        $users = User::orderBy('Nama')->where('Aktif', 1)->get();
        $activeTax = Tax::where('is_active', 1)->first();
        $taxRate = $activeTax ? $activeTax->tax_rate : 0;

        return view('pages.feat.sales.quotation-letter.milenia.create', compact('users', 'taxRate'));
    }

    public function editMilenia($id)
    {
        $quotationLetter = QuotationLetter::with([
            'details.itemMilenia',
            'details.pricelistMilenia',
            'creator',
            'signer'
        ])->findOrFail($id);

        $users = User::orderBy('Nama')->where('Aktif', 1)->get();
        $activeTax = Tax::where('is_active', 1)->first();
        $taxRate = $activeTax ? $activeTax->tax_rate : 0;

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'ubah');

        return view('pages.feat.sales.quotation-letter.milenia.edit', compact('quotationLetter', 'users', 'taxRate'));
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
        $quotationLetter = QuotationLetter::with([
            'details.itemMap',
            'creator',
            'updater',
            'signer'
        ])->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'lihat');

        return view('pages.feat.sales.quotation-letter.map.detail', compact('quotationLetter'));
    }

    public function createMap()
    {
        $users = User::orderBy('Nama')->where('Aktif', 1)->get();
        $activeTax = Tax::where('is_active', 1)->first();
        $taxRate = $activeTax ? $activeTax->tax_rate : 0;

        return view('pages.feat.sales.quotation-letter.map.create', compact('users', 'taxRate'));
    }

    public function editMap($id)
    {
        $quotationLetter = QuotationLetter::with([
            'details.itemMap',
            'details.pricelistMap',
            'creator',
            'signer'
        ])->findOrFail($id);

        $users = User::orderBy('Nama')->where('Aktif', 1)->get();
        $activeTax = Tax::where('is_active', 1)->first();
        $taxRate = $activeTax ? $activeTax->tax_rate : 0;

        // Cek permission sesuai tipe surat
        $this->authorizeLetterAction($quotationLetter, 'ubah');

        return view('pages.feat.sales.quotation-letter.map.edit', compact('quotationLetter', 'users', 'taxRate'));
    }

    public function exportPdf($id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        if ($quotationLetter->letter_type == 'Map') {
            $quotationLetter->load([
                'details.itemMap',
                'details.pricelistMap',
                'creator',
                'signer'
            ]);
        } else {
            $quotationLetter->load([
                'details.itemMilenia',
                'details.pricelistMilenia',
                'creator',
                'signer'
            ]);
        }

        $this->authorizeLetterAction($quotationLetter, 'lihat');

        // Ambil Tax Rate aktif untuk ditampilkan di Note
        $activeTax = Tax::where('is_active', 1)->first();
        $taxRate = $activeTax ? $activeTax->tax_rate : 11;

        if ($quotationLetter->letter_type == 'Milenia') {
            $pdf = Pdf::loadView('pdf.quotation_letter_milenia', compact('quotationLetter', 'taxRate'));
        } else {
            $pdf = Pdf::loadView('pdf.quotation_letter_map', compact('quotationLetter', 'taxRate'));
        }

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Penawaran-' . $quotationLetter->subject . '.pdf');
    }

    public function store(StoreQuotationLetterRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        if ($validated['letter_type'] === 'Milenia' && !$user->can('buat_surat_penawaran_milenia')) {
            abort(403, 'Anda tidak memiliki izin membuat Surat Penawaran Milenia.');
        }
        if ($validated['letter_type'] === 'Map' && !$user->can('buat_surat_penawaran_map')) {
            abort(403, 'Anda tidak memiliki izin membuat Surat Penawaran Map.');
        }

        $userId = Auth::id() ?? 1;
        $signaturePath = null;

        DB::beginTransaction();

        try {

            // Opsi 1: Jika user menggambar (Base64)
            if ($request->filled('signature_base64')) {
                $fileName = 'sig_' . time() . '_' . uniqid() . '.png';
                $relativePath = 'signatures/quotation_letters/' . $fileName;

                $signaturePath = $this->saveSignature($validated['signature_base64'], $relativePath);
            } elseif ($request->hasFile('signature_file')) {
                $signaturePath = $this->uploadFile($request->file('signature_file'), 'signatures/quotation_letters', 'public');

                if (!$signaturePath) {
                    throw new \Exception('Gagal mengunggah file tanda tangan.');
                }
            }

            $quotationLetter = QuotationLetter::create([
                'quotation_letter_number' => $validated['quotation_letter_number'],
                'letter_date'             => $validated['letter_date'],
                'subject'                 => $validated['subject'],

                'recipient_company_name'  => $validated['recipient_company_name'],
                'recipient_attention_to'  => $validated['recipient_attention_to'],
                'recipient_address_line1' => $validated['recipient_address_line1'],
                'recipient_address_line2' => $validated['recipient_address_line2'],
                'recipient_city'          => $validated['recipient_city'],
                'recipient_province'      => $validated['recipient_province'],
                'recipient_postal_code'   => $validated['recipient_postal_code'],

                'letter_type'             => $validated['letter_type'],
                'letter_opening'          => $validated['letter_opening'],
                'letter_note'             => $validated['letter_note'],
                'letter_ending'           => $validated['letter_ending'],

                'signature_id'            => $validated['signature_id'],
                'signature_path'          => $signaturePath,

                'created_by'              => $userId,
                'updated_by'              => null,
            ]);

            if (!$quotationLetter) {
                throw new \Exception('Gagal membuat data header surat.');
            }

            foreach ($validated['items'] as $item) {
                $quotationLetter->details()->create([
                    'item_id'             => $item['item_id'],
                    'item_type'           => $item['item_type'],
                    'sku_number'          => $item['sku_number'],
                    'size_number'         => $item['size_number'] ?? null,
                    'unit_price'          => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'],
                    'total_price'         => $item['total_price'],
                ]);
            }

            DB::commit();

            // Tentukan route redirect
            $route = $validated['letter_type'] === 'Milenia'
                ? 'quotation-letter.milenia.index'
                : 'quotation-letter.map.index';

            $msg = 'Surat Penawaran ' . $validated['letter_type'] . ' berhasil dibuat.';

            return redirect()->route($route)->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($signaturePath) {
                $this->deleteFile($signaturePath, 'public');
            }

            Log::error('Error Store Quotation: ' . $e->getMessage(), [
                'user_id' => $userId,
                'trace'   => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function update(UpdateQuotationLetterRequest $request, $id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        $validated = $request->validated();
        $userId = Auth::id() ?? 1;

        $this->authorizeLetterAction($quotationLetter, 'ubah');

        $newSignaturePath = null;
        $oldSignaturePath = $quotationLetter->signature_path;

        DB::beginTransaction();

        try {
            if ($request->filled('signature_base64')) {
                $fileName = 'sig_' . time() . '_' . uniqid() . '.png';
                $relativePath = 'signatures/quotation_letters/' . $fileName;
                $newSignaturePath = $this->saveSignature($validated['signature_base64'], $relativePath);
            } elseif ($request->hasFile('signature_file')) {
                $newSignaturePath = $this->uploadFile($request->file('signature_file'), 'signatures/quotation_letters', 'public');
                if (!$newSignaturePath) throw new \Exception('Gagal mengunggah file tanda tangan baru.');
            }

            if ($newSignaturePath && $oldSignaturePath) {
                $this->deleteFile($oldSignaturePath, 'public');
            }

            $quotationLetter->update([
                'quotation_letter_number' => $validated['quotation_letter_number'],
                'letter_date'             => $validated['letter_date'],
                'subject'                 => $validated['subject'],

                'recipient_company_name'  => $validated['recipient_company_name'],
                'recipient_attention_to'  => $validated['recipient_attention_to'],
                'recipient_address_line1' => $validated['recipient_address_line1'],
                'recipient_address_line2' => $validated['recipient_address_line2'],
                'recipient_city'          => $validated['recipient_city'],
                'recipient_province'      => $validated['recipient_province'],
                'recipient_postal_code'   => $validated['recipient_postal_code'],

                'letter_opening'          => $validated['letter_opening'],
                'letter_type'             => $validated['letter_type'],
                'letter_note'             => $validated['letter_note'],
                'letter_ending'           => $validated['letter_ending'],

                'signature_id'            => $validated['signature_id'],
                'signature_path'          => $newSignaturePath ?? $oldSignaturePath,

                'updated_by'              => $userId,
            ]);

            $quotationLetter->details()->delete();

            // Insert detail baru
            foreach ($validated['items'] as $item) {
                $quotationLetter->details()->create([
                    'item_id'             => $item['item_id'],
                    'item_type'           => $item['item_type'],
                    'sku_number'          => $item['sku_number'],
                    'size_number'         => $item['size_number'] ?? null,
                    'unit_price'          => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'],
                    'total_price'         => $item['total_price'],
                ]);
            }

            DB::commit();

            $route = $validated['letter_type'] === 'Milenia'
                ? 'quotation-letter.milenia.index'
                : 'quotation-letter.map.index';

            return redirect()
                ->route($route)
                ->with('success', 'Surat Penawaran berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($newSignaturePath) {
                $this->deleteFile($newSignaturePath, 'public');
            }

            Log::error('Error Update Quotation: ' . $e->getMessage(), [
                'user_id' => $userId,
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $quotationLetter = QuotationLetter::findOrFail($id);

        $this->authorizeLetterAction($quotationLetter, 'hapus');

        $letterNumber = $quotationLetter->quotation_letter_number;
        $lettey_type = $quotationLetter->letter_type;
        $filePath = $quotationLetter->signature_path;

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

    // Search API (Handle Duplicate ID & Kirim Real ID)
    public function searchMileniaItems(Request $request)
    {
        $search = $request->get('q');

        $items = ItemMilenia::query()
            ->select(
                'MFIMA.MFIMA_ItemID',
                'MFIMA.MFIMA_Description',
                'SOMPD.SOMPD_PriceAmount',
                'SOMPD.SOMPD_PriceID'
            )
            ->leftJoin('SOMPD', 'MFIMA.MFIMA_ItemID', '=', 'SOMPD.SOMPD_ItemID')
            ->where('MFIMA_Active', 1)
            ->where(function ($query) use ($search) {
                $query->where('MFIMA.MFIMA_ItemID', 'like', "%{$search}%")
                    ->orWhere('MFIMA.MFIMA_Description', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        $results = [];
        foreach ($items as $item) {
            $priceCategory = $item->SOMPD_PriceID ?? 'DEFAULT';

            $uniqueSelect2Id = $item->MFIMA_ItemID . '|' . $priceCategory;

            $results[] = [
                'id' => $uniqueSelect2Id,
                'real_item_id' => $item->MFIMA_ItemID,
                'price_category' => $priceCategory,
                'text' => $item->MFIMA_ItemID . ' - ' . $item->MFIMA_Description . ' (' . $priceCategory . ')',
                'price' => (float) ($item->SOMPD_PriceAmount ?? 0)
            ];
        }

        return response()->json($results);
    }

    public function searchMapItems(Request $request)
    {
        $search = $request->get('q');

        $items = ItemMap::query()
            ->select(
                'MFIMA.MFIMA_ItemID',
                'MFIMA.MFIMA_Description',
                'SOMPD.SOMPD_PriceAmount',
                'SOMPD.SOMPD_PriceID'
            )
            ->leftJoin('SOMPD', 'MFIMA.MFIMA_ItemID', '=', 'SOMPD.SOMPD_ItemID')
            ->where('MFIMA_Active', 1)
            ->where(function ($query) use ($search) {
                $query->where('MFIMA.MFIMA_ItemID', 'like', "%{$search}%")
                    ->orWhere('MFIMA.MFIMA_Description', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        $results = [];
        foreach ($items as $item) {
            $priceCategory = $item->SOMPD_PriceID ?? 'DEFAULT';

            $uniqueSelect2Id = $item->MFIMA_ItemID . '|' . $priceCategory;

            $results[] = [
                'id' => $uniqueSelect2Id,
                'real_item_id' => $item->MFIMA_ItemID,
                'price_category' => $priceCategory,
                'text' => $item->MFIMA_ItemID . ' - ' . $item->MFIMA_Description . ' (' . $priceCategory . ')',
                'price' => (float) ($item->SOMPD_PriceAmount ?? 0)
            ];
        }

        return response()->json($results);
    }
}
