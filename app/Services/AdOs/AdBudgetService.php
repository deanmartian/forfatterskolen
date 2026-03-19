<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdMetricSnapshot;
use App\Models\AdOs\AdActionLog;
use Illuminate\Support\Facades\Log;

class AdBudgetService
{
    public function __construct(
        private readonly AdStrategyService $strategyService,
    ) {}

    public function getCurrentSpendToday(): float
    {
        return (float) AdMetricSnapshot::where('date', today())
            ->where('level', 'campaign')
            ->sum('spend');
    }

    public function getCurrentSpendThisMonth(): float
    {
        return (float) AdMetricSnapshot::whereBetween('date', [now()->startOfMonth(), now()])
            ->where('level', 'campaign')
            ->sum('spend');
    }

    public function getRemainingDailyBudget(): float
    {
        $profile = $this->strategyService->getActiveProfile();
        if (!$profile?->budgetPolicy) return 0;

        return max(0, (float) $profile->budgetPolicy->daily_max - $this->getCurrentSpendToday());
    }

    public function getRemainingMonthlyBudget(): float
    {
        $profile = $this->strategyService->getActiveProfile();
        if (!$profile?->budgetPolicy) return 0;

        return max(0, (float) $profile->budgetPolicy->monthly_max - $this->getCurrentSpendThisMonth());
    }

    public function canIncreaseBudget(int $campaignId, float $proposedIncrease): array
    {
        $campaign = AdCampaign::findOrFail($campaignId);
        $profile = $this->strategyService->getActiveProfile();

        if (!$profile?->budgetPolicy) {
            return ['allowed' => false, 'reason' => 'Ingen budsjettregler konfigurert'];
        }

        $policy = $profile->budgetPolicy;
        $currentBudget = (float) $campaign->daily_budget;
        $newBudget = $currentBudget + $proposedIncrease;
        $increasePercent = $currentBudget > 0 ? ($proposedIncrease / $currentBudget) * 100 : 100;

        // Check daily increase limit
        if ($increasePercent > (float) $policy->max_increase_per_day_percent) {
            $maxAllowed = $currentBudget * ((float) $policy->max_increase_per_day_percent / 100);
            return [
                'allowed' => false,
                'reason' => "Økning på {$increasePercent}% overskrider daglig grense på {$policy->max_increase_per_day_percent}%",
                'max_allowed_increase' => $maxAllowed,
            ];
        }

        // Check single campaign max
        if ($policy->max_single_campaign_budget && $newBudget > (float) $policy->max_single_campaign_budget) {
            return [
                'allowed' => false,
                'reason' => "Nytt budsjett {$newBudget} overskrider maks per kampanje ({$policy->max_single_campaign_budget})",
            ];
        }

        // Check monthly limit
        $monthlyRemaining = $this->getRemainingMonthlyBudget();
        $daysRemaining = max(1, now()->daysInMonth - now()->day + 1);
        $projectedMonthlyFromNew = $newBudget * $daysRemaining;

        if ($projectedMonthlyFromNew > $monthlyRemaining * 1.5) {
            return [
                'allowed' => false,
                'reason' => 'Budsjettøkning ville overskride månedsgrensen',
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    public function generateAllocationRecommendation(): array
    {
        $activeCampaigns = AdCampaign::where('status', 'active')
            ->with('latestMetrics')
            ->get();

        if ($activeCampaigns->isEmpty()) {
            return ['recommendations' => [], 'summary' => 'Ingen aktive kampanjer å allokere budsjett til.'];
        }

        $recommendations = [];
        foreach ($activeCampaigns as $campaign) {
            $metrics = $campaign->latestMetrics;
            if (!$metrics) continue;

            $profile = $this->strategyService->getActiveProfile();
            $targetCpa = $profile?->target_cpa;

            if ($targetCpa && $metrics->cpa) {
                $cpaRatio = (float) $metrics->cpa / (float) $targetCpa;

                if ($cpaRatio < 0.8 && $metrics->conversions >= 3) {
                    $recommendations[] = [
                        'campaign_id' => $campaign->id,
                        'campaign_name' => $campaign->name,
                        'action' => 'increase_budget',
                        'reason' => "CPA ({$metrics->cpa}) er under målsetting ({$targetCpa}). Stabil ytelse.",
                        'confidence' => min(0.95, 0.7 + ($metrics->conversions * 0.02)),
                    ];
                } elseif ($cpaRatio > 1.5 && $metrics->spend > 50) {
                    $recommendations[] = [
                        'campaign_id' => $campaign->id,
                        'campaign_name' => $campaign->name,
                        'action' => 'reduce_budget',
                        'reason' => "CPA ({$metrics->cpa}) er langt over målsetting ({$targetCpa}).",
                        'confidence' => min(0.95, 0.6 + ($metrics->clicks * 0.01)),
                    ];
                }
            }
        }

        return [
            'recommendations' => $recommendations,
            'summary' => count($recommendations) . ' budsjettanbefalinger generert.',
        ];
    }
}
