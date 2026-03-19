<?php

namespace App\Models\AdOs;

use Illuminate\Database\Eloquent\Model;

class AdApprovalPolicy extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'approve_new_campaigns' => 'boolean',
        'approve_budget_increase' => 'boolean',
        'budget_increase_approval_threshold_percent' => 'decimal:2',
        'approve_pause_campaigns' => 'boolean',
        'approve_new_creatives' => 'boolean',
        'approve_targeting_changes' => 'boolean',
        'approve_major_reallocation' => 'boolean',
        'major_reallocation_threshold_percent' => 'decimal:2',
        'auto_approved_action_types' => 'array',
        'emergency_kill_switch' => 'boolean',
    ];

    public function strategyProfile()
    {
        return $this->belongsTo(AdStrategyProfile::class, 'strategy_profile_id');
    }

    public function requiresApproval(string $actionType, array $context = []): bool
    {
        if ($this->emergency_kill_switch) {
            return true;
        }

        $autoApproved = $this->auto_approved_action_types ?? [];
        if (in_array($actionType, $autoApproved)) {
            return false;
        }

        return match ($actionType) {
            'create_campaign' => $this->approve_new_campaigns,
            'increase_budget' => $this->approve_budget_increase && $this->exceedsBudgetThreshold($context),
            'pause_campaign' => $this->approve_pause_campaigns,
            'create_creatives' => $this->approve_new_creatives,
            'change_targeting' => $this->approve_targeting_changes,
            'reallocate_budget' => $this->approve_major_reallocation && $this->exceedsReallocationThreshold($context),
            default => true,
        };
    }

    private function exceedsBudgetThreshold(array $context): bool
    {
        $increasePercent = $context['increase_percent'] ?? 100;
        return $increasePercent >= (float) $this->budget_increase_approval_threshold_percent;
    }

    private function exceedsReallocationThreshold(array $context): bool
    {
        $reallocationPercent = $context['reallocation_percent'] ?? 100;
        return $reallocationPercent >= (float) $this->major_reallocation_threshold_percent;
    }
}
