<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMapBranch extends Model
{
    protected $table = 'SOIVD_Cabang';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMapBranch()
    {
        return $this->belongsTo(SalesMap::class, 'SOIVD_SalesmanID', 'MFSSM_SalesmanID');
    }
}
