<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdRule extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'threshold' => 'decimal:4',
        'min_spend_threshold' => 'decimal:2',
        'auto_apply' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function runs()
    {
        return $this->hasMany(AdRuleRun::class, 'rule_id');
    }

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function evaluate(float $actualValue): bool
    {
        return match ($this->operator) {
            '<' => $actualValue < (float) $this->threshold,
            '<=' => $actualValue <= (float) $this->threshold,
            '>' => $actualValue > (float) $this->threshold,
            '>=' => $actualValue >= (float) $this->threshold,
            '==' => $actualValue == (float) $this->threshold,
            '!=' => $actualValue != (float) $this->threshold,
            default => false,
        };
    }
}
