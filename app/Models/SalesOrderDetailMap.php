<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMap extends Model
{
    protected $table = 'SOIVD';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMap()
    {
        return $this->belongsTo(SalesMap::class, 'SOIVD_SalesmanID', 'MFSSM_SalesmanID');
    }
}
