<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationLetter extends Model
{
    protected $table = 'tt_quotation_letter';

    protected $fillable = [
        'quotation_letter_number',
        'recipient',
        'letter_date',
        'subject',
        'quotation_letter_file',
        'letter_status',
        'letter_type',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'ID');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'ID');
    }
}
