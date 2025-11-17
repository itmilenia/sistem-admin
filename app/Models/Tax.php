<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tr_tax';

    protected $fillable = [
        'tax_name',
        'tax_rate',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
