<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionProgram extends Model
{
    protected $table = 'tt_promotion_program';

    protected $fillable = [
        'program_name',
        'program_description',
        'customer_type',
        'company_type',
        'effective_start_date',
        'effective_end_date',
        'program_file',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_start_date' => 'date',
        'effective_end_date' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PromotionProgramDetail::class, 'promotion_program_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'ID');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'ID');
    }
}
