<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdStrategyProfile;
use App\Models\AdOs\AdBudgetPolicy;
use App\Models\AdOs\AdRiskPolicy;
use App\Models\AdOs\AdApprovalPolicy;
use App\Models\AdOs\AdActionLog;

class AdStrategyService
{
    public function getActiveProfile(): ?AdStrategyProfile
    {
        return AdStrategyProfile::where('is_active', true)
            ->with(['budgetPolicy', 'riskPolicy', 'approvalPolicy'])
            ->first();
    }

    public function getAllProfiles()
    {
        return AdStrategyProfile::with(['budgetPolicy', 'riskPolicy', 'approvalPolicy'])
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->get();
    }

    public function createOrUpdateProfile(array $data): AdStrategyProfile
    {
        $profileData = collect($data)->only([
            'name', 'automation_level', 'primary_goal', 'monthly_budget',
            'daily_budget_ceiling', 'target_cpa', 'target_roas',
            'min_conversion_volume', 'risk_tolerance', 'preferred_campaign_types',
            'priority_products', 'is_active', 'notes',
        ])->toArray();

        if (isset($data['id']) && $data['id']) {
            $profile = AdStrategyProfile::findOrFail($data['id']);
            $profile->update($profileData);
        } else {
            // Deactivate other profiles if this one is active
            if ($profileData['is_active'] ?? true) {
                AdStrategyProfile::where('is_active', true)->update(['is_active' => false]);
            }
            $profile = AdStrategyProfile::create($profileData);
        }

        // Budget policy
        if (isset($data['budget'])) {
            AdBudgetPolicy::updateOrCreate(
                ['strategy_profile_id' => $profile->id],
                $data['budget']
            );
        }

        // Risk policy
        if (isset($data['risk'])) {
            AdRiskPolicy::updateOrCreate(
                ['strategy_profile_id' => $profile->id],
                $data['risk']
            );
        }

        // Approval policy
        if (isset($data['approval'])) {
            AdApprovalPolicy::updateOrCreate(
                ['strategy_profile_id' => $profile->id],
                $data['approval']
            );
        }

        AdActionLog::log('strategy_updated', [
            'target_type' => 'strategy_profile',
            'target_id' => $profile->id,
            'triggered_by' => 'human',
            'user_id' => auth()->id(),
            'payload' => ['profile_name' => $profile->name],
        ]);

        return $profile->fresh(['budgetPolicy', 'riskPolicy', 'approvalPolicy']);
    }

    public function getAutomationLevel(): string
    {
        $profile = $this->getActiveProfile();
        return $profile?->automation_level ?? config('ad_os.default_automation_mode', 'assisted');
    }

    public function isEmergencyKillSwitchActive(): bool
    {
        $profile = $this->getActiveProfile();
        return $profile?->approvalPolicy?->emergency_kill_switch ?? false;
    }
}
