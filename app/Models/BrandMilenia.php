<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandMilenia extends Model
{
    protected $table = 'MFIB';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'MFIB_BrandID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function mileniaItems()
    {
        return $this->hasMany(ItemMilenia::class, 'MFIMA_Brand', 'MFIB_BrandID');
    }
}
