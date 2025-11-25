<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMilenia extends Model
{
    protected $table = 'MFIMA';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'MFIMA_ItemID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function mileniaBrands()
    {
        return $this->belongsTo(BrandMilenia::class, 'MFIMA_Brand', 'MFIB_BrandID');
    }

    public function pricelistMilenia()
    {
        return $this->hasOne(PricelistMilenia::class, 'SOMPD_ItemID', 'MFIMA_ItemID')
            ->orderBy('SOMPD_UPDATE', 'desc');
    }
}
