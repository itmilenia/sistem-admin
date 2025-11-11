<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $table = 'tr_product_brands';
    protected $connection = 'db_product_trainer';

    protected $fillable = [
        'brand_name',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
