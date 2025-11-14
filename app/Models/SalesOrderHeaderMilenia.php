<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderHeaderMilenia extends Model
{
    protected $table = 'SOIVH';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'SOIVH_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function SalesOrderMileniadetail()
    {
        return $this->hasMany(SalesOrderDetailMilenia::class, 'SOIVD_InvoiceID', 'SOIVH_InvoiceID');
    }
}
