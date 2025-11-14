<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistMilenia extends Model
{
    protected $table = 'SOMPD';
    protected $connection = 'sqlsrv_wh';

    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'SOMPD_UPDATE' => 'datetime',
    ];

    public function ItemMilenia()
    {
        return $this->belongsTo(ItemMilenia::class, 'SOMPD_ItemID', 'MFIMA_ItemID');
    }
}
