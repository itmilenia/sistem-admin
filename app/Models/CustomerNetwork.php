<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerNetwork extends Model
{
    protected $table = 'tr_network_customers';
    protected $connection = 'db_product_trainer';

    protected $fillable = [
        'name',
        'category',
        'brand_id',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'brand_id'  => 'array',
    ];
    // Accessor untuk ambil collection ProductBrand
    public function getBrandsCollectionAttribute()
    {
        $ids = $this->brand_id ?? [];
        return ProductBrand::whereIn('id', $ids)->get();
    }
}
