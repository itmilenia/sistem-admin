<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionProgramRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'program_name' => 'required|string|max:255',
            'customer_type' => 'required|string|in:JARINGAN,NON_JARINGAN,JARINGAN_DAN_NON_JARINGAN',
            'company_type' => 'required|string|in:PT Mega Auto Prima,PT Milenia Mega Mandiri',
            'effective_start_date' => 'required|date',
            'effective_end_date' => 'required|date|after_or_equal:effective_start_date',
            'is_active' => 'required|boolean',
            'program_description' => 'required|string',
            'program_file' => 'required|file|mimes:pdf|max:10240',
            'items' => 'required|array|min:1',
            'items.*' => 'required|string|exists:sqlsrv_wh.MFIMA,MFIMA_ItemID',
        ];
    }

    public function messages()
    {
        return [
            'program_name.required' => 'Nama program tidak boleh kosong.',

            'customer_type.required' => 'Tipe customer wajib dipilih.',
            'customer_type.in' => 'Tipe customer yang dipilih tidak valid.',

            'company_type.required' => 'Perusahaan wajib dipilih.',
            'company_type.in' => 'Perusahaan yang dipilih tidak valid.',

            'effective_start_date.required' => 'Tanggal mulai tidak boleh kosong.',
            'effective_end_date.required' => 'Tanggal selesai tidak boleh kosong.',
            'effective_end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',

            'is_active.required' => 'Status aktif wajib dipilih.',
            'is_active.boolean' => 'Status aktif yang dipilih tidak valid.',

            'program_description.required' => 'Deskripsi program tidak boleh kosong.',

            'program_file.required' => 'File program wajib diunggah.',
            'program_file.file' => 'File program harus berupa file.',
            'program_file.mimes' => 'Format file program harus PDF.',
            'program_file.max' => 'Ukuran file program tidak boleh lebih dari 10 MB.',

            'items.required' => 'Item tidak boleh kosong.',
            'items.*.required' => 'Item tidak boleh kosong.',
            'items.*.exists' => 'Item yang dipilih tidak valid.',
        ];
    }
}
