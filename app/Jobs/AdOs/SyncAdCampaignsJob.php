<?php

namespace App\Jobs\AdOs;

use App\Models\AdOs\AdAccount;
use App\Models\AdOs\AdCampaign;
use App\Services\AdOs\AdCampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAdCampaignsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300];

    public function __construct(
        private readonly ?int $accountId = null,
    ) {}

    public function handle(AdCampaignService $campaignService): void
    {
        $accounts = $this->accountId
            ? AdAccount::where('id', $this->accountId)->where('status', 'active')->get()
            : AdAccount::where('status', 'active')->get();

        foreach ($accounts as $account) {
            try {
                $platformService = $campaignService->getPlatformService($account->platform);
                $campaigns = $platformService->syncCampaigns($account->id);

                foreach ($campaigns as $campaignData) {
                    AdCampaign::updateOrCreate(
                        [
                            'account_id' => $account->id,
                            'external_id' => $campaignData['id'],
                        ],
                        [
                            'platform' => $account->platform,
                            'name' => $campaignData['name'] ?? 'Unnamed',
                            'objective' => $this->mapObjective($campaignData['objective'] ?? ''),
                            'status' => $this->mapStatus($campaignData['status'] ?? ''),
                            'daily_budget' => isset($campaignData['daily_budget']) ? $campaignData['daily_budget'] / 100 : null,
                            'total_budget' => isset($campaignData['lifetime_budget']) ? $campaignData['lifetime_budget'] / 100 : null,
                            'platform_meta' => $campaignData,
                        ]
                    );
                }

                $account->update([
                    'last_synced_at' => now(),
                    'sync_state' => ['campaigns_synced' => count($campaigns), 'synced_at' => now()->toIso8601String()],
                ]);
            } catch (\Exception $e) {
                Log::error('AdOS SyncCampaigns failed', [
                    'account_id' => $account->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function mapObjective(string $fbObjective): string
    {
        return match ($fbObjective) {
            'OUTCOME_LEADS', 'LEAD_GENERATION' => 'leads',
            'OUTCOME_SALES', 'CONVERSIONS' => 'conversions',
            'OUTCOME_TRAFFIC', 'LINK_CLICKS' => 'traffic',
            'OUTCOME_AWARENESS', 'BRAND_AWARENESS', 'REACH' => 'awareness',
            'OUTCOME_ENGAGEMENT', 'POST_ENGAGEMENT' => 'engagement',
            default => 'leads',
        };
    }

    private function mapStatus(string $fbStatus): string
    {
        return match ($fbStatus) {
            'ACTIVE' => 'active',
            'PAUSED' => 'paused',
            'ARCHIVED', 'DELETED' => 'archived',
            default => 'draft',
        };
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('AdOS SyncAdCampaignsJob failed permanently', ['error' => $exception->getMessage()]);
    }
}
