<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdAccount;
use App\Models\AdOs\AdActionLog;
use Illuminate\Support\Facades\Log;

class AdCampaignService
{
    public function __construct(
        private readonly AdStrategyService $strategyService,
        private readonly AdApprovalService $approvalService,
    ) {}

    public function getAllCampaigns(array $filters = [])
    {
        $query = AdCampaign::with(['account', 'latestMetrics']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }
        if (isset($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }

        return $query->orderByDesc('updated_at')->paginate(20);
    }

    public function getCampaign(int $id): AdCampaign
    {
        return AdCampaign::with(['account', 'adSets.ads.creative', 'metrics', 'decisions', 'experiments'])
            ->findOrFail($id);
    }

    public function createDraft(array $data): AdCampaign
    {
        $campaign = AdCampaign::create(array_merge($data, [
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]));

        AdActionLog::log('campaign_draft_created', [
            'target_type' => 'campaign',
            'target_id' => $campaign->id,
            'triggered_by' => isset($data['ai_brief']) ? 'ai' : 'human',
            'user_id' => auth()->id(),
            'payload' => ['name' => $campaign->name, 'platform' => $campaign->platform],
        ]);

        return $campaign;
    }

    public function updateStatus(int $campaignId, string $newStatus, string $triggeredBy = 'human'): AdCampaign
    {
        $campaign = AdCampaign::findOrFail($campaignId);
        $oldStatus = $campaign->status;

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'active') $updateData['published_at'] = now();
        if ($newStatus === 'paused') $updateData['paused_at'] = now();

        $campaign->update($updateData);

        AdActionLog::log('campaign_status_changed', [
            'target_type' => 'campaign',
            'target_id' => $campaign->id,
            'triggered_by' => $triggeredBy,
            'user_id' => auth()->id(),
            'payload' => ['from' => $oldStatus, 'to' => $newStatus],
        ]);

        return $campaign->fresh();
    }

    public function updateBudget(int $campaignId, float $newBudget, string $triggeredBy = 'human'): AdCampaign
    {
        $campaign = AdCampaign::findOrFail($campaignId);
        $oldBudget = $campaign->daily_budget;

        // Enforce guardrails
        $profile = $this->strategyService->getActiveProfile();
        if ($profile?->budgetPolicy) {
            $policy = $profile->budgetPolicy;
            $maxIncrease = $policy->maxBudgetIncreaseToday((float) $oldBudget);
            $increase = $newBudget - (float) $oldBudget;

            if ($increase > $maxIncrease && $triggeredBy !== 'human') {
                Log::warning('AdOS budget increase blocked by guardrail', [
                    'campaign_id' => $campaignId,
                    'requested' => $newBudget,
                    'max_allowed' => (float) $oldBudget + $maxIncrease,
                ]);
                $newBudget = (float) $oldBudget + $maxIncrease;
            }

            if ($policy->max_single_campaign_budget && $newBudget > (float) $policy->max_single_campaign_budget) {
                $newBudget = (float) $policy->max_single_campaign_budget;
            }
        }

        $campaign->update(['daily_budget' => $newBudget]);

        AdActionLog::log('campaign_budget_changed', [
            'target_type' => 'campaign',
            'target_id' => $campaign->id,
            'triggered_by' => $triggeredBy,
            'user_id' => auth()->id(),
            'payload' => ['from' => $oldBudget, 'to' => $newBudget],
        ]);

        return $campaign->fresh();
    }

    public function getDashboardStats(): array
    {
        $campaigns = AdCampaign::all();

        return [
            'total' => $campaigns->count(),
            'active' => $campaigns->where('status', 'active')->count(),
            'paused' => $campaigns->where('status', 'paused')->count(),
            'draft' => $campaigns->where('status', 'draft')->count(),
            'pending_approval' => $campaigns->where('status', 'pending_approval')->count(),
            'total_daily_budget' => $campaigns->where('status', 'active')->sum('daily_budget'),
            'total_spent' => $campaigns->sum('spent_total'),
        ];
    }

    public function getPlatformService(string $platform): AdPlatformInterface
    {
        return match ($platform) {
            'facebook' => app(AdFacebookPlatformService::class),
            'google' => app(AdGooglePlatformService::class),
            default => throw new \InvalidArgumentException("Unknown platform: {$platform}"),
        };
    }
}
