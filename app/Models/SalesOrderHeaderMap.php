<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderHeaderMap extends Model
{
    protected $table = 'SOIVH';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'SOIVH_InvoiceID';
    protected $keyType = 'string';
    public $timestamps = false;

    public function SalesOrderMapdetail()
    {
        return $this->hasMany(SalesOrderDetailMap::class, 'SOIVD_InvoiceID', 'SOIVH_InvoiceID');
    }
}
