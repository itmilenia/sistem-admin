<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimProductDetail extends Model
{
    use HasFactory;

    protected $table = 'tt_product_claim_detail';
    protected $fillable = [
        'product_claim_id',
        'invoice_id',
        'product_id',
        'quantity',
        'order_date',
        'delivery_date',
        'return_reason',
    ];

    /**
     * Each claim detail belongs to one product claim.
     */
    public function productClaim()
    {
        return $this->belongsTo(ClaimProduct::class, 'product_claim_id', 'id');
    }
}
