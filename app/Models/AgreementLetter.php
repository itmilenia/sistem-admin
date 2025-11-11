<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgreementLetter extends Model
{
    protected $table = 'tt_agreement_letter';

    protected $fillable = [
        'customer_id',
        'company_type',
        'sales_name',
        'effective_start_date',
        'effective_end_date',
        'agreement_letter_path',
        'letter_status',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'effective_start_date' => 'date',
        'effective_end_date'   => 'date',
    ];

    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(){
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerNetwork::class, 'customer_id');
    }
}
