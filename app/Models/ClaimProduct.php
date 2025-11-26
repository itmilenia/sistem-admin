<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimProduct extends Model
{
    use HasFactory;

    protected $table = 'tt_product_claim';
    protected $fillable = [
        'company_type',
        'claim_type',
        'sales_id',
        'sales_head_id',
        'checker_id',
        'retail_name',
        'claim_date',
        'verification_date',
        'verification_result',
        'checker_signature_path',
        'sales_signature_path',
        'sales_head_signature_path',
        'created_by',
        'updated_by',
    ];

    /**
     * One claim has many claim details.
     */
    public function claimDetails()
    {
        return $this->hasMany(ClaimProductDetail::class, 'product_claim_id', 'id');
    }

    /**
     * The salesperson who created the claim.
     */
    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id', 'ID');
    }

    /**
     * The head of sales who supervises the salesperson.
     */
    public function salesHead()
    {
        return $this->belongsTo(User::class, 'sales_head_id', 'ID');
    }

    /**
     * The checker (verifier) of the claim.
     */
    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id', 'ID');
    }

    /**
     * The user who created the record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'ID');
    }

    /**
     * The user who last updated the record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'ID');
    }
}
