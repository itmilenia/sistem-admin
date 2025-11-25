<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationLetterDetail extends Model
{
    protected $table = 'tt_quotation_letter_detail';

    protected $fillable = [
        'quotation_letter_id',
        'item_id',
        'item_type',
        'sku_number',
        'warranty_period',
        'size_number',
        'unit_price',
        'discount_percentage',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function quotationLetter()
    {
        return $this->belongsTo(QuotationLetter::class, 'quotation_letter_id');
    }

    public function itemMap()
    {
        return $this->belongsTo(ItemMap::class, 'item_id', 'MFIMA_ItemID');
    }

    public function itemMilenia()
    {
        return $this->belongsTo(ItemMilenia::class, 'item_id', 'MFIMA_ItemID');
    }

    public function pricelistMap()
    {
        return $this->belongsTo(PricelistMap::class, 'item_id', 'SOMPD_ItemID');
    }

    public function pricelistMilenia()
    {
        return $this->belongsTo(PricelistMilenia::class, 'item_id', 'SOMPD_ItemID');
    }
}
