<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Aturan untuk Header
            'company_type' => 'required|string|max:255',
            'sales_id' => 'required|integer|exists:users,ID',
            'sales_head_id' => 'required|integer|exists:users,ID',
            'checker_id' => 'required|integer|exists:users,ID',
            'retail_name' => 'required|string|max:255',
            'claim_date' => 'required|date',

            // Aturan untuk Detail (sebagai array)
            'details' => 'required|array|min:1',
            'details.*.invoice_id' => 'required|string|max:255',

            // Validasi cross-database ke 'sqlsrv_wh'
            'details.*.product_id' => 'required|string|exists:sqlsrv_wh.MFIMA,MFIMA_ItemID',

            'details.*.quantity' => 'required|integer|min:1',
            'details.*.order_date' => 'required|date',
            'details.*.delivery_date' => 'required|date|after_or_equal:details.*.order_date',
            'details.*.return_reason' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'company_type.required' => 'Tipe perusahaan wajib dipilih.',

            'sales_id.required' => 'Sales wajib dipilih.',
            'sales_id.exists' => 'Sales tidak ditemukan.',

            'sales_head_id.required' => 'Head Sales wajib dipilih.',
            'sales_head_id.exists' => 'Head Sales tidak ditemukan.',

            'checker_id.required' => 'Trainer wajib dipilih.',
            'checker_id.exists' => 'Trainer tidak ditemukan.',

            'retail_name.required' => 'Nama toko wajib diisi.',
            'retail_name.max' => 'Nama toko tidak boleh lebih dari 255 karakter.',

            'claim_date.required' => 'Tgl. Klaim wajib diisi.',
            'claim_date.date' => 'Format Tgl. Klaim tidak valid.',

            'details.required' => 'Detail wajib diisi.',
            'details.array' => 'Detail harus berupa array.',
            'details.min' => 'Detail minimal memiliki 1 item.',

            'details.*.invoice_id.required' => 'Nomor faktur wajib diisi.',
            'details.*.invoice_id.max' => 'Nomor faktur tidak boleh lebih dari 255 karakter.',

            'details.*.product_id.required' => 'Produk wajib dipilih.',
            'details.*.product_id.exists' => 'Produk tidak ditemukan.',

            'details.*.quantity.required' => 'Jumlah wajib diisi.',
            'details.*.quantity.integer' => 'Jumlah harus berupa angka.',
            'details.*.quantity.min' => 'Jumlah minimal 1.',

            'details.*.order_date.required' => 'Tgl. Order wajib diisi.',
            'details.*.order_date.date' => 'Format Tgl. Order tidak valid.',

            'details.*.delivery_date.required' => 'Tgl. Pengiriman wajib diisi.',
            'details.*.delivery_date.date' => 'Format Tgl. Pengiriman tidak valid.',
            'details.*.delivery_date.after_or_equal' => 'Tgl. Pengiriman harus setelah Tgl. Order.',
            
            'details.*.return_reason.required' => 'Alasan pengembalian wajib diisi.',
            'details.*.return_reason.max' => 'Alasan pengembalian tidak boleh lebih dari 255 karakter.',
        ];
    }
}
