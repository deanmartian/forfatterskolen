<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdRuleRun extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'triggered' => 'boolean',
        'actual_value' => 'decimal:4',
        'details' => 'array',
    ];

    public function rule()
    {
        return $this->belongsTo(AdRule::class, 'rule_id');
    }
}
