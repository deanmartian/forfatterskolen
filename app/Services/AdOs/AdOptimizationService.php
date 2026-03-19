<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdMetricSnapshot;
use App\Models\AdOs\AdAiDecision;
use App\Models\AdOs\AdActionLog;
use Illuminate\Support\Facades\Log;

class AdOptimizationService
{
    public function __construct(
        private readonly AdStrategyService $strategyService,
    ) {}

    public function analyzeCampaign(AdCampaign $campaign): array
    {
        $metrics = AdMetricSnapshot::where('campaign_id', $campaign->id)
            ->where('level', 'campaign')
            ->orderByDesc('date')
            ->limit(7)
            ->get();

        if ($metrics->isEmpty()) {
            return ['status' => 'insufficient_data', 'signals' => []];
        }

        $signals = [];
        $latest = $metrics->first();
        $profile = $this->strategyService->getActiveProfile();

        // Detect high CPA
        if ($profile?->target_cpa && $latest->cpa && (float) $latest->cpa > (float) $profile->target_cpa * 1.3) {
            $signals[] = [
                'type' => 'high_cpa',
                'severity' => (float) $latest->cpa > (float) $profile->target_cpa * 2 ? 'critical' : 'warning',
                'message' => "CPA ({$latest->cpa}) er over målsetting ({$profile->target_cpa})",
                'value' => (float) $latest->cpa,
                'threshold' => (float) $profile->target_cpa,
            ];
        }

        // Detect low CTR
        if ($latest->impressions > 1000 && $latest->ctr && (float) $latest->ctr < 0.005) {
            $signals[] = [
                'type' => 'low_ctr',
                'severity' => 'warning',
                'message' => 'CTR er under 0.5% - vurder nye kreative elementer',
                'value' => (float) $latest->ctr,
            ];
        }

        // Detect wasted spend (spend without conversions)
        $riskPolicy = $profile?->riskPolicy;
        if ($riskPolicy?->max_spend_without_conversion) {
            $recentSpend = $metrics->sum('spend');
            $recentConversions = $metrics->sum('conversions');
            if ($recentConversions == 0 && $recentSpend > (float) $riskPolicy->max_spend_without_conversion) {
                $signals[] = [
                    'type' => 'wasted_spend',
                    'severity' => 'critical',
                    'message' => "Brukt {$recentSpend} uten konverteringer",
                    'value' => (float) $recentSpend,
                ];
            }
        }

        // Detect fatigue (declining CTR over time)
        if ($metrics->count() >= 4) {
            $recentCtr = $metrics->take(3)->avg('ctr');
            $olderCtr = $metrics->skip(3)->avg('ctr');
            if ($olderCtr > 0 && $recentCtr > 0 && $recentCtr < $olderCtr * 0.7) {
                $signals[] = [
                    'type' => 'creative_fatigue',
                    'severity' => 'warning',
                    'message' => 'CTR synker - mulig annonseslitasje',
                    'value' => round(((float) $olderCtr - (float) $recentCtr) / (float) $olderCtr * 100, 1),
                ];
            }
        }

        // Detect winner (strong stable performance)
        if ($profile?->target_cpa && $latest->cpa && (float) $latest->cpa < (float) $profile->target_cpa * 0.7 && $latest->conversions >= 5) {
            $signals[] = [
                'type' => 'winner',
                'severity' => 'positive',
                'message' => "Sterk ytelse - CPA ({$latest->cpa}) godt under mål. Vurder skalering.",
                'value' => (float) $latest->cpa,
            ];
        }

        // Detect low ROAS
        if ($profile?->target_roas && $latest->roas && (float) $latest->roas < (float) $profile->min_roas_threshold) {
            $signals[] = [
                'type' => 'low_roas',
                'severity' => 'warning',
                'message' => "ROAS ({$latest->roas}) er under minimum ({$profile->target_roas})",
                'value' => (float) $latest->roas,
            ];
        }

        return [
            'status' => count($signals) > 0 ? 'action_needed' : 'healthy',
            'signals' => $signals,
            'metrics_summary' => [
                'impressions_7d' => $metrics->sum('impressions'),
                'clicks_7d' => $metrics->sum('clicks'),
                'spend_7d' => $metrics->sum('spend'),
                'conversions_7d' => $metrics->sum('conversions'),
                'avg_cpa' => $metrics->avg('cpa'),
                'avg_ctr' => $metrics->avg('ctr'),
                'avg_roas' => $metrics->avg('roas'),
            ],
        ];
    }

    public function analyzeAllCampaigns(): array
    {
        $campaigns = AdCampaign::where('status', 'active')->get();
        $results = [];

        foreach ($campaigns as $campaign) {
            $results[$campaign->id] = array_merge(
                ['campaign_name' => $campaign->name, 'platform' => $campaign->platform],
                $this->analyzeCampaign($campaign)
            );
        }

        return $results;
    }
}
