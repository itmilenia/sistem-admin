<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesMilenia extends Model
{
    protected $table = 'MFSSM';
    protected $connection = 'sqlsrv_wh';
    protected $primaryKey = 'MFSSM_SalesmanID';
    protected $keyType = 'string';
    public $timestamps = false;
}
