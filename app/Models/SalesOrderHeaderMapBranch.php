<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderHeaderMapBranch extends Model
{
    protected $table = 'SOIVH_Cabang';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'SOIVH_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function SalesOrderMapBranchdetail()
    {
        return $this->hasMany(SalesOrderDetailMapBranch::class, 'SOIVD_InvoiceID', 'SOIVH_InvoiceID');
    }
}
