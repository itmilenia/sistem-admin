<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailMilenia extends Model
{
    protected $table = 'SOIVD';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'SOIVD_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function salesManMilenia()
    {
        return $this->belongsTo(SalesMilenia::class, 'SOIVD_SalesmanID', 'MFSSM_SalesmanID');
    }
}
