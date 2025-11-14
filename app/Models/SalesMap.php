<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesMap extends Model
{
    protected $table = 'MFSSM';
    protected $connection = 'sqlsrv_snx';
    protected $primaryKey = 'MFSSM_SalesmanID';
    protected $keyType = 'string';
    public $timestamps = false;
}
