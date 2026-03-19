<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdRiskPolicy extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'stop_loss_daily' => 'decimal:2',
        'stop_loss_weekly' => 'decimal:2',
        'max_cpa_threshold' => 'decimal:2',
        'min_roas_threshold' => 'decimal:2',
        'max_spend_without_conversion' => 'decimal:2',
        'auto_pause_losers' => 'boolean',
        'auto_scale_winners' => 'boolean',
    ];

    public function strategyProfile()
    {
        return $this->belongsTo(AdStrategyProfile::class, 'strategy_profile_id');
    }

    public function isCpaExceeded(float $cpa): bool
    {
        return $this->max_cpa_threshold && $cpa > (float) $this->max_cpa_threshold;
    }

    public function isRoasBelowMinimum(float $roas): bool
    {
        return $this->min_roas_threshold && $roas < (float) $this->min_roas_threshold;
    }
}
