<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Exception;

trait SignatureUploadTrait
{
    /**
     * Menyimpan tanda tangan dari data Base64.
     *
     * @param string $base64String Data gambar base64 (contoh: 'data:image/png;base64,iVBOR...')
     * @param string $relativePath Path relatif untuk menyimpan file di 'public' disk.
     * @return string $relativePath Path yang sama jika berhasil.
     * @throws \Exception Jika data base64 tidak valid.
     */
    public function saveSignature(string $base64String, string $relativePath): string
    {
        // 1. Pisahkan data header (data:image/png;base64) dari datanya
        @list($type, $data) = explode(';', $base64String);
        @list(, $data)      = explode(',', $data);

        // 2. Validasi jika datanya ada
        if (empty($data)) {
            throw new Exception('Data tanda tangan base64 tidak valid atau kosong.');
        }

        // 3. Decode data
        $decodedData = base64_decode($data);

        // 4. Simpan ke storage
        Storage::disk('public')->put($relativePath, $decodedData);

        // 5. Kembalikan path
        return $relativePath;
    }
}
