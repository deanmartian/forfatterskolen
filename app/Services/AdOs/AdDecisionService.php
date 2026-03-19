<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdAiDecision;
use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdActionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdDecisionService
{
    public function __construct(
        private readonly AdStrategyService $strategyService,
        private readonly AdOptimizationService $optimizationService,
    ) {}

    public function generateDecisions(): array
    {
        $profile = $this->strategyService->getActiveProfile();
        if (!$profile) return [];

        if ($profile->approvalPolicy?->emergency_kill_switch) {
            Log::info('AdOS: Emergency kill switch active - no decisions generated');
            return [];
        }

        $analyses = $this->optimizationService->analyzeAllCampaigns();
        $decisions = [];

        foreach ($analyses as $campaignId => $analysis) {
            if ($analysis['status'] === 'insufficient_data') continue;

            foreach ($analysis['signals'] as $signal) {
                $decision = $this->signalToDecision($campaignId, $signal, $analysis, $profile);
                if ($decision) {
                    $decisions[] = $decision;
                }
            }
        }

        return $decisions;
    }

    public function getRecommendations(int $limit = 20)
    {
        return AdAiDecision::where('status', 'pending')
            ->orderByDesc('confidence')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->with('campaign')
            ->get();
    }

    public function executeDecision(int $decisionId): array
    {
        $decision = AdAiDecision::findOrFail($decisionId);

        if ($decision->status !== 'approved' && $decision->status !== 'pending') {
            return ['success' => false, 'error' => 'Beslutning er ikke i riktig status for utførelse'];
        }

        // Check if it can auto-execute
        if ($decision->requires_approval && $decision->status === 'pending') {
            return ['success' => false, 'error' => 'Beslutning krever godkjenning først'];
        }

        try {
            $result = $this->performAction($decision);

            $decision->update([
                'status' => 'executed',
                'executed_at' => now(),
                'execution_result' => $result,
            ]);

            AdActionLog::log($decision->decision_type, [
                'target_type' => 'campaign',
                'target_id' => $decision->campaign_id,
                'triggered_by' => 'ai',
                'decision_id' => $decision->id,
                'payload' => $decision->proposed_action,
                'result' => $result,
            ]);

            return ['success' => true, 'result' => $result];
        } catch (\Exception $e) {
            $decision->update([
                'status' => 'failed',
                'execution_result' => ['error' => $e->getMessage()],
            ]);

            Log::error('AdOS decision execution failed', [
                'decision_id' => $decisionId,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function signalToDecision(int $campaignId, array $signal, array $analysis, $profile): ?AdAiDecision
    {
        $campaign = AdCampaign::find($campaignId);
        if (!$campaign) return null;

        $decision = match ($signal['type']) {
            'high_cpa' => [
                'decision_type' => 'reduce_budget',
                'confidence' => $signal['severity'] === 'critical' ? 0.90 : 0.75,
                'reasoning_summary' => $signal['message'],
                'risk_level' => $signal['severity'] === 'critical' ? 'high' : 'medium',
                'proposed_action' => [
                    'campaign_id' => $campaignId,
                    'change' => 'reduce_budget',
                    'reduction_percent' => $signal['severity'] === 'critical' ? 30 : 15,
                ],
            ],
            'wasted_spend' => [
                'decision_type' => 'pause_campaign',
                'confidence' => 0.88,
                'reasoning_summary' => $signal['message'],
                'risk_level' => 'high',
                'proposed_action' => [
                    'campaign_id' => $campaignId,
                    'change' => 'pause',
                ],
            ],
            'creative_fatigue' => [
                'decision_type' => 'create_new_variants',
                'confidence' => 0.80,
                'reasoning_summary' => $signal['message'],
                'risk_level' => 'low',
                'proposed_action' => [
                    'campaign_id' => $campaignId,
                    'change' => 'generate_new_creatives',
                    'count' => 5,
                ],
            ],
            'winner' => [
                'decision_type' => 'increase_budget',
                'confidence' => 0.85,
                'reasoning_summary' => $signal['message'],
                'risk_level' => 'medium',
                'proposed_action' => [
                    'campaign_id' => $campaignId,
                    'change' => 'increase_budget',
                    'increase_percent' => 15,
                ],
            ],
            'low_ctr' => [
                'decision_type' => 'create_new_variants',
                'confidence' => 0.70,
                'reasoning_summary' => $signal['message'],
                'risk_level' => 'low',
                'proposed_action' => [
                    'campaign_id' => $campaignId,
                    'change' => 'generate_new_creatives',
                    'count' => 3,
                ],
            ],
            default => null,
        };

        if (!$decision) return null;

        $automationLevel = $campaign->automation_level ?? $profile->automation_level;
        $requiresApproval = $this->determineApprovalRequired($decision, $automationLevel, $profile);

        return AdAiDecision::create(array_merge($decision, [
            'requires_approval' => $requiresApproval,
            'campaign_id' => $campaignId,
            'context_data' => [
                'signals' => [$signal],
                'metrics_summary' => $analysis['metrics_summary'] ?? [],
            ],
            'status' => 'pending',
        ]));
    }

    private function determineApprovalRequired(array $decision, string $automationLevel, $profile): bool
    {
        if ($automationLevel === 'manual') return true;
        if ($automationLevel === 'assisted') return true;

        if ($automationLevel === 'supervised') {
            return in_array($decision['risk_level'], ['high', 'critical']);
        }

        // full_operator - only critical needs approval
        if ($automationLevel === 'full_operator') {
            return $decision['risk_level'] === 'critical';
        }

        return true;
    }

    private function performAction(AdAiDecision $decision): array
    {
        $action = $decision->proposed_action;
        $campaignId = $action['campaign_id'] ?? $decision->campaign_id;

        return match ($decision->decision_type) {
            'pause_campaign' => $this->performPause($campaignId),
            'reduce_budget' => $this->performBudgetChange($campaignId, -($action['reduction_percent'] ?? 15)),
            'increase_budget' => $this->performBudgetChange($campaignId, $action['increase_percent'] ?? 15),
            default => ['action' => $decision->decision_type, 'status' => 'noted'],
        };
    }

    private function performPause(int $campaignId): array
    {
        $campaign = AdCampaign::findOrFail($campaignId);
        $campaign->update(['status' => 'paused', 'paused_at' => now()]);
        return ['action' => 'paused', 'campaign_id' => $campaignId];
    }

    private function performBudgetChange(int $campaignId, float $changePercent): array
    {
        $campaign = AdCampaign::findOrFail($campaignId);
        $currentBudget = (float) $campaign->daily_budget;
        $newBudget = round($currentBudget * (1 + $changePercent / 100), 2);
        $newBudget = max(0, $newBudget);

        $campaign->update(['daily_budget' => $newBudget]);

        return [
            'action' => $changePercent > 0 ? 'budget_increased' : 'budget_reduced',
            'from' => $currentBudget,
            'to' => $newBudget,
            'change_percent' => $changePercent,
        ];
    }
}
