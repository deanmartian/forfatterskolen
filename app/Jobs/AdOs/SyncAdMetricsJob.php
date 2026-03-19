<?php

namespace App\Jobs\AdOs;

use App\Models\AdOs\AdAccount;
use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdMetricSnapshot;
use App\Services\AdOs\AdCampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAdMetricsJob implements ShouldQueue
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
                $metricsData = $platformService->syncMetrics(
                    $account->id,
                    now()->subDays(7)->toDateString(),
                    now()->toDateString()
                );

                foreach ($metricsData as $row) {
                    $campaign = AdCampaign::where('external_id', $row['campaign_id'] ?? null)
                        ->where('account_id', $account->id)
                        ->first();

                    if (!$campaign) continue;

                    AdMetricSnapshot::updateOrCreate(
                        [
                            'level' => 'campaign',
                            'reference_id' => $campaign->id,
                            'date' => $row['date_start'] ?? now()->toDateString(),
                        ],
                        [
                            'campaign_id' => $campaign->id,
                            'impressions' => $row['impressions'] ?? 0,
                            'clicks' => $row['clicks'] ?? 0,
                            'spend' => (float) ($row['spend'] ?? 0),
                            'conversions' => $this->extractConversions($row),
                            'ctr' => (float) ($row['ctr'] ?? 0),
                            'cpc' => (float) ($row['cpc'] ?? 0),
                            'platform_data' => $row,
                        ]
                    );
                }

                $account->update(['last_synced_at' => now()]);
            } catch (\Exception $e) {
                Log::error('AdOS SyncMetrics failed for account', [
                    'account_id' => $account->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function extractConversions(array $row): int
    {
        if (isset($row['actions'])) {
            foreach ($row['actions'] as $action) {
                if (in_array($action['action_type'] ?? '', ['lead', 'offsite_conversion.fb_pixel_lead', 'complete_registration'])) {
                    return (int) ($action['value'] ?? 0);
                }
            }
        }
        return (int) ($row['conversions'] ?? 0);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('AdOS SyncAdMetricsJob failed permanently', ['error' => $exception->getMessage()]);
    }
}
