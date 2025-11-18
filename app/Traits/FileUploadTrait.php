<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait FileUploadTrait
{
    /**
     * Menyimpan file yang diunggah ke disk.
     *
     * @param UploadedFile $file Objek file dari request.
     * @param string $directory Folder tujuan di dalam disk (e.g., 'avatars', 'documents').
     * @param string $disk Storage disk yang digunakan (e.g., 'public', 's3').
     * @return string|false Path ke file yang disimpan, atau false jika gagal.
     */
    public function uploadFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): string|false
    {
        if (!$file->isValid()) {
            return false;
        }

        try {
            // Menggunakan store() akan otomatis menghasilkan nama file unik (hashed)
            // Ini adalah cara paling aman untuk menghindari konflik/penimpaan file.
            $path = $file->store($directory, $disk);

            return $path;
        } catch (Exception $e) {
            Log::error('Gagal mengunggah file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus file lama dari disk.
     *
     * @param string|null $path Path file yang akan dihapus (e.g., 'avatars/namalama.jpg').
     * @param string $disk Storage disk tempat file berada.
     * @return bool True jika berhasil dihapus atau path null, false jika gagal.
     */
    public function deleteFile(string $path = null, string $disk = 'public'): bool
    {
        if (is_null($path)) {
            return true;
        }

        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }

            // Jika file sudah tidak ada, anggap berhasil
            return true;
        } catch (Exception $e) {
            Log::error('Gagal menghapus file: ' . $e->getMessage());
            return false;
        }
    }
}
