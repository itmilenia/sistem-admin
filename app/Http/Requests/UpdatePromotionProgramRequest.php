<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePromotionProgramRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'program_name' => [
                'required',
                'string',
                'max:255',
            ],
            'customer_type' => 'required|string|max:100',
            'company_type' => 'required|string|max:100',
            'effective_start_date' => 'required|date',
            'effective_end_date' => 'required|date|after_or_equal:effective_start_date',
            'is_active' => 'required|boolean',
            'program_description' => 'required|string',
            'program_file' => 'nullable|file|mimes:pdf|max:10240',
            'items' => 'required|array|min:1',
            'items.*' => 'required|string|exists:sqlsrv_wh.MFIMA,MFIMA_ItemID',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'program_name.required' => 'Nama program tidak boleh kosong.',
            'program_name.unique' => 'Nama program ini sudah digunakan. Mohon ganti.',

            'customer_type.required' => 'Tipe customer wajib dipilih.',

            'company_type.required' => 'Tipe perusahaan wajib dipilih.',

            'effective_start_date.required' => 'Tanggal mulai tidak boleh kosong.',
            'effective_end_date.required' => 'Tanggal selesai tidak boleh kosong.',
            'effective_end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',

            'is_active.required' => 'Status aktif wajib dipilih.',
            'is_active.boolean' => 'Status aktif yang dipilih tidak valid.',

            'program_description.required' => 'Deskripsi program tidak boleh kosong.',

            'program_file.file' => 'File lampiran harus berupa file.',
            'program_file.mimes' => 'File lampiran harus berformat PDF.',
            'program_file.max' => 'Ukuran file lampiran maksimal 10MB.',

            'items.required' => 'Anda harus memilih minimal satu item promosi.',
            'items.min' => 'Anda harus memilih minimal satu item promosi.',
            'items.*.exists' => 'Item ID yang dipilih tidak valid atau tidak ditemukan.',
        ];
    }
}
