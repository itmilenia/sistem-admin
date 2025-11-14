<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderHeaderMileniaBranch extends Model
{
    protected $table = 'SOIVH_Cabang';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'SOIVH_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function SalesOrderMileniaBranchdetail()
    {
        return $this->hasMany(SalesOrderDetailMileniaBranch::class, 'SOIVD_InvoiceID', 'SOIVH_InvoiceID');
    }
}
