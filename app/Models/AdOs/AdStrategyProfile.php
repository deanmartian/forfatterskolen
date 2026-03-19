<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdStrategyProfile extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'monthly_budget' => 'decimal:2',
        'daily_budget_ceiling' => 'decimal:2',
        'target_cpa' => 'decimal:2',
        'target_roas' => 'decimal:2',
        'preferred_campaign_types' => 'array',
        'priority_products' => 'array',
        'is_active' => 'boolean',
    ];

    public function budgetPolicy()
    {
        return $this->hasOne(AdBudgetPolicy::class, 'strategy_profile_id');
    }

    public function riskPolicy()
    {
        return $this->hasOne(AdRiskPolicy::class, 'strategy_profile_id');
    }

    public function approvalPolicy()
    {
        return $this->hasOne(AdApprovalPolicy::class, 'strategy_profile_id');
    }

    public function accounts()
    {
        return $this->hasMany(AdAccount::class, 'strategy_profile_id');
    }

    public function isFullyConfigured(): bool
    {
        return $this->budgetPolicy && $this->riskPolicy && $this->approvalPolicy;
    }
}
