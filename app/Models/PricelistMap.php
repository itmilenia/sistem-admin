<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistMap extends Model
{
    protected $table = 'SOMPD';
    protected $connection = 'sqlsrv_snx';

    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'SOMPD_UPDATE' => 'datetime',
    ];

    public function itemMap()
    {
        return $this->belongsTo(ItemMap::class, 'SOMPD_ItemID', 'MFIMA_ItemID');
    }
}
