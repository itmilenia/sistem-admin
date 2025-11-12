<?php

namespace App\Http\Controllers\Feat;

use App\Models\ItemMilenia;
use Illuminate\Http\Request;
use App\Models\PromotionProgram;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePromotionProgramRequest;
use App\Http\Requests\UpdatePromotionProgramRequest;
use App\Models\ItemMap;

class PromotionProgramController extends Controller
{
    private function authorizePromotionAction(PromotionProgram $promotion, string $action)
    {
        $user = auth()->user();

        if ($promotion->company_type === 'PT Milenia Mega Mandiri' && !$user->can("{$action}_program_promo_milenia")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Program Promo Milenia.');
        }

        if ($promotion->company_type === 'PT Mega Auto Prima' && !$user->can("{$action}_program_promo_map")) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses Program Promo MAP.');
        }
    }

    public function landing()
    {
        return view('pages.feat.promo-produk.promotion-program.landing');
    }

    public function indexMilenia()
    {
        $promotionPrograms = PromotionProgram::with(['details', 'createdBy', 'updatedBy'])
            ->orderBy('effective_start_date', 'desc')
            ->where('company_type', 'PT Milenia Mega Mandiri')
            ->get();

        return view('pages.feat.promo-produk.promotion-program.milenia.index', compact('promotionPrograms'));
    }

    public function showMilenia($id)
    {
        $promotionProgram = PromotionProgram::with([
            'details.itemMilenia.mileniaBrands',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizePromotionAction($promotionProgram, 'lihat');

        return view('pages.feat.promo-produk.promotion-program.milenia.detail', compact('promotionProgram'));
    }

    public function searchItemsMilenia(Request $request)
    {
        $search = $request->input('q');

        if (empty($search)) {
            return response()->json(['results' => []]);
        }

        // Cari berdasarkan ItemID atau ItemName
        $items = ItemMilenia::select('MFIMA_ItemID', 'MFIMA_Description')
            ->where(function ($query) use ($search) {
                $query->where('MFIMA_ItemID', 'like', "%{$search}%")
                    ->orWhere('MFIMA_Description', 'like', "%{$search}%");
            })
            ->where('MFIMA_Active', 1)
            ->orderBy('MFIMA_Description', 'asc')
            ->take(50)
            ->get();

        // Format data agar sesuai dengan Select2
        $formattedItems = $items->map(function ($item) {
            return [
                'id' => $item->MFIMA_ItemID,
                'text' => $item->MFIMA_ItemID . ' - ' . $item->MFIMA_Description
            ];
        });

        return response()->json(['results' => $formattedItems]);
    }

    public function createMilenia()
    {
        $items = ItemMilenia::select('MFIMA_ItemID', 'MFIMA_Description')
            ->orderBy('MFIMA_Description', 'asc')
            ->where('MFIMA_Active', 1)
            ->get();

        return view('pages.feat.promo-produk.promotion-program.milenia.create', compact('items'));
    }

    public function editMilenia($id)
    {
        // Ambil data promotion program berdasarkan ID
        $promotionProgram = PromotionProgram::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizePromotionAction($promotionProgram, 'lihat');

        $promotionProgram->load(['details']);

        $selectedItemIds = $promotionProgram->details->pluck('item_id');
        $selectedItems = ItemMilenia::select('MFIMA_ItemID', 'MFIMA_Description')
            ->whereIn('MFIMA_ItemID', $selectedItemIds)
            ->get();

        return view('pages.feat.promo-produk.promotion-program.milenia.edit', compact('promotionProgram', 'selectedItems'));
    }

    public function indexMap()
    {
        $promotionPrograms = PromotionProgram::with(['details', 'createdBy', 'updatedBy'])
            ->orderBy('effective_start_date', 'desc')
            ->where('company_type', 'PT Mega Auto Prima')
            ->get();

        return view('pages.feat.promo-produk.promotion-program.map.index', compact('promotionPrograms'));
    }

    public function showMap($id)
    {
        $promotionProgram = PromotionProgram::with([
            'details.itemMap.mapBrands',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizePromotionAction($promotionProgram, 'lihat');

        return view('pages.feat.promo-produk.promotion-program.map.detail', compact('promotionProgram'));
    }

    public function searchItemsMap(Request $request)
    {
        $search = $request->input('q');

        if (empty($search)) {
            return response()->json(['results' => []]);
        }

        // Cari berdasarkan ItemID atau ItemName
        $items = ItemMap::select('MFIMA_ItemID', 'MFIMA_Description')
            ->where(function ($query) use ($search) {
                $query->where('MFIMA_ItemID', 'like', "%{$search}%")
                    ->orWhere('MFIMA_Description', 'like', "%{$search}%");
            })
            ->where('MFIMA_Active', 1)
            ->orderBy('MFIMA_Description', 'asc')
            ->take(50)
            ->get();

        // Format data agar sesuai dengan Select2
        $formattedItems = $items->map(function ($item) {
            return [
                'id' => $item->MFIMA_ItemID,
                'text' => $item->MFIMA_ItemID . ' - ' . $item->MFIMA_Description
            ];
        });

        return response()->json(['results' => $formattedItems]);
    }

    public function createMap()
    {
        $items = ItemMap::select('MFIMA_ItemID', 'MFIMA_Description')
            ->orderBy('MFIMA_Description', 'asc')
            ->where('MFIMA_Active', 1)
            ->get();

        return view('pages.feat.promo-produk.promotion-program.map.create', compact('items'));
    }

    public function editMap($id)
    {
        // Ambil data promotion program berdasarkan ID
        $promotionProgram = PromotionProgram::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizePromotionAction($promotionProgram, 'lihat');

        $promotionProgram->load(['details']);

        $selectedItemIds = $promotionProgram->details->pluck('item_id');
        $selectedItems = ItemMilenia::select('MFIMA_ItemID', 'MFIMA_Description')
            ->whereIn('MFIMA_ItemID', $selectedItemIds)
            ->get();

        return view('pages.feat.promo-produk.promotion-program.map.edit', compact('promotionProgram', 'selectedItems'));
    }

    public function store(StorePromotionProgramRequest $request)
    {
        // 1. Validasi dan Otorisasi sudah ditangani oleh StorePromotionProgramRequest
        $validated = $request->validated();

        // Ambil user dan ID-nya
        $user = auth()->user();
        $userId = $user->ID;

        // 1. Cek permission berdasarkan company_type (mengadaptasi dari referensi)
        if ($validated['company_type'] === 'PT Milenia Mega Mandiri' && !$user->can('buat_program_promo_milenia')) {
            abort(403, 'Anda tidak memiliki izin membuat Program Promo Milenia.');
        }

        if ($validated['company_type'] === 'PT Mega Auto Prima' && !$user->can('buat_program_promo_map')) {
            abort(403, 'Anda tidak memiliki izin membuat Program Promo MAP.');
        }

        $file = $request->file('program_file');
        $filePath = null;

        DB::beginTransaction(); // Mulai Transaksi

        try {
            // 2. Penyimpanan File
            if ($file) {
                $fileExtension = $file->getClientOriginalExtension();

                // Membuat nama file yang unik: PromoProgram-[TipeCustomer]-[Timestamp].pdf
                $fileName = 'PromoProgram-' . str_replace('_', '', $validated['customer_type']) . '-' . date('YmdHis') . '.' . $fileExtension;

                // Folder penyimpanan
                $folderPath = 'promotion_programs';
                $filePath = $file->storeAs($folderPath, $fileName, 'public');

                if (!$filePath) {
                    throw new \Exception('Gagal menyimpan file lampiran ke storage.');
                }
            }

            // 3. Buat Program Promosi (Header)
            $promotionProgram = PromotionProgram::create([
                'program_name' => $validated['program_name'],
                'customer_type' => $validated['customer_type'],
                'company_type' => $validated['company_type'],
                'effective_start_date' => $validated['effective_start_date'],
                'effective_end_date' => $validated['effective_end_date'],
                'is_active' => $validated['is_active'],
                'program_description' => $validated['program_description'],
                'program_file' => $filePath,
                'created_by' => $userId,
                'updated_by' => null
            ]);

            if (!$promotionProgram) {
                throw new \Exception('Gagal menyimpan data header program promosi.');
            }

            // 4. Buat Detail Program (Items)
            $itemDetails = [];
            foreach ($validated['items'] as $itemId) {
                $itemDetails[] = [
                    'promotion_program_id' => $promotionProgram->id,
                    'item_id' => $itemId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert bulk untuk performa
            $promotionProgram->details()->insert($itemDetails);

            DB::commit();

            // Arahkan ke index dengan pesan sukses berdasarkan company_type

            if ($validated['company_type'] === 'PT Milenia Mega Mandiri') {
                return redirect()->route('promotion-program.milenia.index')->with('success', 'Program promosi berhasil ditambahkan.');
            } else {
                return redirect()->route('promotion-program.map.index')->with('success', 'Program promosi berhasil ditambahkan.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file yang terlanjur di-upload jika terjadi error DB
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Catat error ke log
            Log::error('Gagal menyimpan program promosi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId ?? null,
                'input' => $request->except('program_file'),
            ]);

            // Kembalikan ke form create dengan pesan error dan input sebelumnya
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi. Detail: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(UpdatePromotionProgramRequest $request, $id)
    {
        $promotionProgram = PromotionProgram::findOrFail($id);

        // Cek permission sesuai tipe surat
        $this->authorizePromotionAction($promotionProgram, 'ubah');

        // 1. Validasi dan Otorisasi ditangani oleh UpdatePromotionProgramRequest
        $validated = $request->validated();
        $userId = auth()->user()->ID;

        $oldFilePath = $promotionProgram->program_file;
        $newFilePath = $oldFilePath;

        DB::beginTransaction(); // Mulai Transaksi

        try {
            // 2. Handle Upload File (Jika ada file baru)
            if ($request->hasFile('program_file')) {
                $file = $request->file('program_file');
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = 'PromoProgram-' . str_replace('_', '', $validated['customer_type']) . '-' . date('YmdHis') . '.' . $fileExtension;
                $folderPath = 'promotion_programs';

                // Simpan file baru
                $newFilePath = $file->storeAs($folderPath, $fileName, 'public');

                if (!$newFilePath) {
                    throw new \Exception('Gagal menyimpan file lampiran baru ke storage.');
                }
            }

            // 3. Update Program Promosi (Header)
            $promotionProgram->update([
                'program_name' => $validated['program_name'],
                'customer_type' => $validated['customer_type'],
                'company_type' => $validated['company_type'],
                'effective_start_date' => $validated['effective_start_date'],
                'effective_end_date' => $validated['effective_end_date'],
                'is_active' => $validated['is_active'],
                'program_description' => $validated['program_description'] ?? null,
                'program_file' => $newFilePath,
                'updated_by' => $userId,
            ]);

            // 4. Sinkronisasi Detail Program (Items)
            // Hapus detail lama
            $promotionProgram->details()->delete();

            // Siapkan detail baru
            $itemDetails = [];
            foreach ($validated['items'] as $itemId) {
                $itemDetails[] = [
                    'promotion_program_id' => $promotionProgram->id,
                    'item_id' => $itemId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Masukkan (insert) detail baru
            $promotionProgram->details()->insert($itemDetails);

            DB::commit();

            // 5. Hapus File Lama (JIKA file baru berhasil di-upload DAN file lama ada)
            if ($newFilePath != $oldFilePath && $oldFilePath) {
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }

            // Arahkan ke index dengan pesan sukses berdasarkan company_type

            if ($validated['company_type'] === 'PT Milenia Mega Mandiri') {
                return redirect()->route('promotion-program.milenia.index')->with('success', 'Program promosi berhasil diperbarui.');
            } else {
                return redirect()->route('promotion-program.map.index')->with('success', 'Program promosi berhasil diperbarui.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file BARU yang terlanjur di-upload jika terjadi error DB
            if ($newFilePath != $oldFilePath && $newFilePath) {
                if (Storage::disk('public')->exists($newFilePath)) {
                    Storage::disk('public')->delete($newFilePath);
                }
            }

            // Catat error ke log
            Log::error('Gagal memperbarui program promosi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId ?? null,
                'program_id' => $promotionProgram->id,
                'input' => $request->except('program_file'),
            ]);

            // Kembalikan ke form edit dengan pesan error
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi. Detail: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $promotionProgram = PromotionProgram::findOrFail($id);

        // 1. Otorisasi (Pastikan Anda punya permission ini di sistem Anda)
        $this->authorizePromotionAction($promotionProgram, 'hapus');


        $companyType = $promotionProgram->company_type;
        $oldFilePath = $promotionProgram->program_file;
        $programName = $promotionProgram->program_name;

        DB::beginTransaction(); // Mulai Transaksi

        try {
            // 2. Hapus detail program
            $promotionProgram->details()->delete();

            // 3. Hapus program utama
            $promotionProgram->delete();

            DB::commit();

            // 4. Hapus file lampiran dari storage SETELAH transaksi DB berhasil
            if ($oldFilePath) {
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }

            // 5. Redirect ke index dengan pesan sukses berdasarkan companyType
            if ($companyType === 'PT Milenia Mega Mandiri') {
                return redirect()->route('promotion-program.milenia.index')
                    ->with('success', 'Program promosi "' . $programName . '" dan data terkait berhasil dihapus.');
            } else {
                return redirect()->route('promotion-program.map.index')
                    ->with('success', 'Program promosi "' . $programName . '" dan data terkait berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            // Catat error ke log
            Log::error('Gagal menghapus program promosi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'program_id' => $promotionProgram->id,
            ]);

            // 6. Redirect kembali dengan pesan error
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus program "' . $programName . '". Detail: ' . $e->getMessage());
        }
    }
}
