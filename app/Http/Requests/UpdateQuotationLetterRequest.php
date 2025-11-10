<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for updating a quotation letter
     *
     */
    public function rules(): array
    {
        return [
            'quotation_letter_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tt_quotation_letter', 'quotation_letter_number')->ignore($this->route('id')),
            ],
            'recipient' => ['required', 'string', 'max:255'],
            'letter_date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'quotation_letter_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'letter_type' => ['required', 'string', 'in:Milenia,Map'],
            'letter_status' => ['required', 'string', 'in:Sudah Terkirim,Belum Terkirim'],
        ];
    }

    /**
     * Return custom validation error messages.
     *
     * This method is used by the FormRequest class to define custom validation error messages.
     *
     */
    public function messages(): array
    {
        return [
            'quotation_letter_number.required' => 'Nomor Surat Penawaran wajib diisi.',
            'quotation_letter_number.unique' => 'Nomor Surat Penawaran sudah terdaftar, silakan gunakan nomor lain.',
            'recipient.required' => 'Nama Penerima wajib diisi.',
            'letter_date.required' => 'Tanggal Surat wajib diisi.',
            'letter_date.date' => 'Format Tanggal Surat tidak valid.',
            'subject.required' => 'Perihal wajib diisi.',

            'quotation_letter_file.file' => 'Input harus berupa file.',
            'quotation_letter_file.mimes' => 'File harus berekstensi PDF.',
            'quotation_letter_file.max' => 'Ukuran file tidak boleh melebihi 10MB.',

            'letter_type.required' => 'Tipe Surat wajib diisi.',
            'letter_type.in' => 'Tipe Surat yang dipilih tidak valid.',

            'letter_status.required' => 'Status Surat wajib diisi.',
            'letter_status.in' => 'Status Surat yang dipilih tidak valid.',
        ];
    }
}
