<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandMap extends Model
{
    protected $table = 'MFIB';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'MFIB_BrandID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function mapItems()
    {
        return $this->hasMany(ItemMap::class, 'MFIMA_Brand', 'MFIB_BrandID');
    }
}
