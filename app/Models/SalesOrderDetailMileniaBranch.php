<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMileniaBranch extends Model
{
    protected $table = 'SOIVD_Cabang';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMileniaBranch()
    {
        return $this->belongsTo(SalesMilenia::class, 'SOIVD_SalesmanID', 'MFSSM_SalesmanID');
    }
}
