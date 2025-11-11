<?php

namespace App\Http\Requests;

use App\Models\CustomerNetwork;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerNetworkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $connection = (new CustomerNetwork())->getConnectionName();

        $allowedCategories = ['ULTIME', 'MAD', 'POD', 'KEY_ACCOUNT', 'RESSELER', 'SAD'];
        $productBrandTable = 'tr_product_brands';

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique("{$connection}.tr_network_customers", 'name')->ignore($this->route('id')),
            ],
            'category' => ['required', 'array', 'min:1'],
            'category.*' => ['string', Rule::in($allowedCategories)],

            'brand_id' => ['required', 'array', 'min:1'],
            'brand_id.*' => [
                'integer',
                Rule::exists("{$connection}.{$productBrandTable}", 'id')
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama Customer harus diisi.',
            'name.unique' => 'Nama Customer sudah terdaftar, silakan gunakan nama lain.',
            'category.required' => 'Kategori Customer harus dipilih.',
            'category.*.in' => 'Kategori Customer yang dipilih tidak valid.',
            'brand_id.required' => 'Brand harus dipilih.',
            'brand_id.*.exists' => 'Brand yang dipilih tidak valid.',
            'is_active.required' => 'Status harus dipilih.',
            'is_active.boolean' => 'Status yang dipilih tidak valid.',
        ];
    }
}
