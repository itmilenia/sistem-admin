<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // --- Header ---
            'quotation_letter_number' => ['required', 'string', 'max:100', 'unique:tt_quotation_letter,quotation_letter_number'],
            'letter_date'             => ['required', 'date'],
            'subject'                 => ['required', 'string', 'max:255'],

            // --- Recipient ---
            'recipient_company_name'  => ['required', 'string', 'max:255'],
            'recipient_attention_to'  => ['required', 'string', 'max:255'],
            'recipient_address_line1' => ['nullable', 'string', 'max:255'],
            'recipient_address_line2' => ['nullable', 'string', 'max:255'],
            'recipient_city'          => ['required', 'string', 'max:100'],
            'recipient_province'      => ['required', 'string', 'max:100'],
            'recipient_postal_code'   => ['required', 'string', 'max:20'],

            // --- Metadata ---
            'letter_type'             => ['required', 'string', 'in:Milenia,Map'],
            'letter_opening'          => ['required', 'string'],
            'letter_ending'           => ['required', 'string'],
            'letter_note'             => ['nullable', 'string'],
            'signature_id'            => ['required', 'integer', 'exists:users,ID'],
            'signature_base64'          => ['nullable', 'string'],
            'signature_file'          => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],

            // --- Detail Items ---
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.item_id'             => ['required', 'string'],
            'items.*.item_type'         => ['required', 'string', 'max:255'],
            'items.*.sku_number'          => ['required', 'string'],
            'items.*.size_number'         => ['nullable', 'string'],
            'items.*.unit_price'          => ['required', 'numeric', 'min:0'],
            'items.*.discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.total_price'         => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            // --- Header Messages ---
            'quotation_letter_number.required' => 'Nomor Surat Penawaran wajib diisi.',
            'quotation_letter_number.string'   => 'Nomor Surat Penawaran harus berupa teks.',
            'quotation_letter_number.max'      => 'Nomor Surat Penawaran maksimal 100 karakter.',
            'quotation_letter_number.unique'   => 'Nomor Surat Penawaran sudah terdaftar, silakan gunakan nomor lain.',

            'letter_date.required' => 'Tanggal Surat wajib diisi.',
            'letter_date.date'     => 'Format Tanggal Surat tidak valid.',

            'subject.required' => 'Perihal surat wajib diisi.',
            'subject.string'   => 'Perihal surat harus berupa teks.',
            'subject.max'      => 'Perihal surat maksimal 255 karakter.',

            // --- Recipient Messages ---
            'recipient_company_name.required' => 'Nama Perusahaan penerima wajib diisi.',
            'recipient_company_name.max'      => 'Nama Perusahaan maksimal 255 karakter.',

            'recipient_attention_to.required' => 'Nama UP (Attention To) wajib diisi.',
            'recipient_attention_to.max'      => 'Nama UP maksimal 255 karakter.',

            'recipient_address_line1.max' => 'Alamat baris 1 maksimal 255 karakter.',
            'recipient_address_line2.max' => 'Alamat baris 2 maksimal 255 karakter.',

            'recipient_city.required' => 'Kota penerima wajib diisi.',
            'recipient_city.max'      => 'Nama Kota maksimal 100 karakter.',

            'recipient_province.required' => 'Provinsi penerima wajib diisi.',
            'recipient_province.max'      => 'Nama Provinsi maksimal 100 karakter.',

            'recipient_postal_code.required' => 'Kode Pos penerima wajib diisi.',
            'recipient_postal_code.max'      => 'Kode Pos maksimal 20 karakter.',

            // --- Metadata Messages ---
            'letter_type.required' => 'Tipe Surat wajib dipilih.',
            'letter_type.in'       => 'Tipe Surat harus berupa Milenia atau Map.',

            'signature_id.required' => 'Penanda tangan (Signer) wajib dipilih.',
            'signature_id.integer'  => 'ID Penanda tangan tidak valid.',
            'signature_id.exists'   => 'Penanda tangan yang dipilih tidak ditemukan di database.',

            'signature_base64.string'   => 'Penanda tangan harus berupa teks.',

            'signature_file.file'     => 'Penanda tangan harus berupa file.',
            'signature_file.mimes'    => 'Penanda tangan harus berupa file dengan ekstensi jpg, jpeg, atau png.',
            'signature_file.max'      => 'Penanda tangan maksimal 2MB.',

            'letter_note.string' => 'Catatan Surat harus berupa teks.',

            // --- Detail Items Messages ---
            'items.required' => 'Daftar barang (items) tidak boleh kosong.',
            'items.array'    => 'Format daftar barang salah.',
            'items.min'      => 'Minimal harus ada 1 barang dalam penawaran.',

            'items.*.item_id.required' => 'Salah satu Item ID pada daftar barang belum diisi.',
            'items.*.item_id.string'   => 'Item ID harus berupa teks.',

            'items.*.sku_number.required' => 'SKU Number pada daftar barang wajib diisi.',

            'items.*.item_type.required' => 'Item Type pada daftar barang wajib diisi.',
            'items.*.item_type.string' => 'Item Type harus berupa teks.',


            'items.*.unit_price.required' => 'Harga Satuan barang wajib diisi.',
            'items.*.unit_price.numeric'  => 'Harga Satuan harus berupa angka.',
            'items.*.unit_price.min'      => 'Harga Satuan tidak boleh negatif.',

            'items.*.discount_percentage.required' => 'Diskon (%) wajib diisi (isi 0 jika tidak ada).',
            'items.*.discount_percentage.numeric'  => 'Diskon harus berupa angka.',
            'items.*.discount_percentage.min'      => 'Diskon tidak boleh kurang dari 0%.',
            'items.*.discount_percentage.max'      => 'Diskon tidak boleh lebih dari 100%.',

            'items.*.total_price.required' => 'Total Harga baris wajib diisi.',
            'items.*.total_price.numeric'  => 'Total Harga harus berupa angka.',
            'items.*.total_price.min'      => 'Total Harga tidak boleh negatif.',
        ];
    }
}
