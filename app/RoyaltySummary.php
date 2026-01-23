<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoyaltySummary extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'quarter',
        'project_registration_id',
        'sales_amount',
        'cost_amount_base',
        'cost_amount_multiplied',
        'net_amount',
        'computed_at',
    ];

    protected $casts = [
        'sales_amount' => 'decimal:2',
        'cost_amount_base' => 'decimal:2',
        'cost_amount_multiplied' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'computed_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
