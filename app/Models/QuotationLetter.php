<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationLetter extends Model
{
    protected $table = 'tt_quotation_letter';

    protected $fillable = [
        'quotation_letter_number',
        'letter_date',
        'subject',

        'recipient_company_name',
        'recipient_attention_to',
        'recipient_address_line1',
        'recipient_address_line2',
        'recipient_city',
        'recipient_province',
        'recipient_postal_code',

        'letter_type',
        'letter_opening',
        'letter_note',
        'letter_ending',

        'signature_id',
        'signature_path',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(QuotationLetterDetail::class, 'quotation_letter_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'ID');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'ID');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signature_id', 'ID');
    }
}
