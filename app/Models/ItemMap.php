<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMap extends Model
{
    protected $table = 'MFIMA';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'MFIMA_ItemID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function mapBrands()
    {
        return $this->belongsTo(BrandMap::class, 'MFIMA_Brand', 'MFIB_BrandID');
    }
}
