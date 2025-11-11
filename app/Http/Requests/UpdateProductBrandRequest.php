<?php

namespace App\Http\Requests;

use App\Models\ProductBrand; // 1. Pastikan model di-import
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductBrandRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Ambil info koneksi dan tabel langsung dari model
        $model = new ProductBrand();
        $connection = $model->getConnectionName();
        $table = $model->getTable();

        return [
            'brand_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique("{$connection}.{$table}", 'brand_name')->ignore($this->route('id')),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'brand_name.required' => 'Nama brand harus diisi.',
            'brand_name.unique' => 'Nama brand sudah ada.',
            'is_active.required' => 'Status brand harus diisi.',
            'is_active.boolean' => 'Status brand tidak valid.',
        ];
    }
}
