<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateAgreementLetterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer_id' => [
                'required',
                'integer',
            ],
            'company_type' => [
                'required',
                'string',
                Rule::in(['PT Milenia Mega Mandiri', 'PT Mega Auto Prima']),
            ],
            'sales_name' => ['required', 'string', 'max:255'],
            'effective_start_date' => ['required', 'date'],
            'effective_end_date' => ['required', 'date', 'after_or_equal:effective_start_date'],
            'letter_status' => [
                'required',
                'string',
                Rule::in(['Sudah Terkirim', 'Belum Terkirim']),
            ],
            'agreement_letter_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'Customer harus dipilih.',
            'customer_id.integer' => 'Customer yang dipilih tidak valid.',

            'company_type.required' => 'Tipe perusahaan harus dipilih.',
            'company_type.in' => 'Tipe perusahaan yang dipilih tidak valid.',

            'sales_name.required' => 'Nama sales harus diisi.',
            'sales_name.max' => 'Nama sales tidak boleh lebih dari 255 karakter.',

            'effective_start_date.required' => 'Tanggal mulai berlaku harus diisi.',
            'effective_start_date.date' => 'Format tanggal mulai berlaku tidak valid.',

            'effective_end_date.required' => 'Tanggal berakhir harus diisi.',
            'effective_end_date.date' => 'Format tanggal berakhir tidak valid.',
            'effective_end_date.after_or_equal' => 'Tanggal berakhir harus setelah tanggal mulai berlaku.',

            'letter_status.required' => 'Status surat harus dipilih.',
            'letter_status.in' => 'Status surat yang dipilih tidak valid.',

            'agreement_letter_file.file' => 'File Surat harus berupa file.',
            'agreement_letter_file.mimes' => 'File Surat harus berekstensi PDF.',
            'agreement_letter_file.max' => 'Ukuran file Surat tidak boleh lebih dari 10 MB.',

            'is_active.required' => 'Status aktif harus dipilih.',
            'is_active.boolean' => 'Status aktif yang dipilih tidak valid.',
        ];
    }
}
