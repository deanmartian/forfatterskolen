<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdBudgetPolicy extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'monthly_max' => 'decimal:2',
        'daily_max' => 'decimal:2',
        'max_increase_per_day_percent' => 'decimal:2',
        'max_increase_per_week_percent' => 'decimal:2',
        'max_single_campaign_budget' => 'decimal:2',
        'min_campaign_budget' => 'decimal:2',
        'allow_auto_rebalance' => 'boolean',
    ];

    public function strategyProfile()
    {
        return $this->belongsTo(AdStrategyProfile::class, 'strategy_profile_id');
    }

    public function isWithinDailyLimit(float $currentSpend): bool
    {
        return $currentSpend <= (float) $this->daily_max;
    }

    public function isWithinMonthlyLimit(float $currentSpend): bool
    {
        return $currentSpend <= (float) $this->monthly_max;
    }

    public function maxBudgetIncreaseToday(float $currentBudget): float
    {
        return $currentBudget * ((float) $this->max_increase_per_day_percent / 100);
    }
}
