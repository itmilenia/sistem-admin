<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionProgramDetail extends Model
{
    protected $table = 'tt_promotion_program_detail';

    protected $fillable = [
        'promotion_program_id',
        'item_id',
    ];

    public function promotionProgram()
    {
        return $this->belongsTo(PromotionProgram::class, 'promotion_program_id');
    }

    public function itemMilenia()
    {
        return $this->belongsTo(ItemMilenia::class, 'item_id', 'MFIMA_ItemID');
    }
}
