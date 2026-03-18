<?php

namespace App\Jobs;

use App\Models\AdCampaign;
use App\Models\AdCampaignStat;
use App\Services\FacebookAdsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAdStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 2;

    public function handle(): void
    {
        $campaigns = AdCampaign::where('status', 'active')
            ->whereNotNull('external_campaign_id')
            ->get();

        foreach ($campaigns as $campaign) {
            try {
                if ($campaign->platform === 'facebook') {
                    $this->syncFacebookStats($campaign);
                }
                // Google Ads stats kan legges til her
            } catch (\Exception $e) {
                Log::error('Ad stats sync feilet', [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function syncFacebookStats(AdCampaign $campaign): void
    {
        $fb = app(FacebookAdsService::class);
        $insights = $fb->getCampaignStats($campaign->external_campaign_id);

        if (! $insights) {
            return;
        }

        $leads = 0;
        if (isset($insights['actions'])) {
            foreach ($insights['actions'] as $action) {
                if (($action['action_type'] ?? '') === 'lead') {
                    $leads = (int) ($action['value'] ?? 0);
                }
            }
        }

        $spend = (float) ($insights['spend'] ?? 0);

        AdCampaignStat::updateOrCreate(
            ['ad_campaign_id' => $campaign->id, 'date' => today()],
            [
                'impressions' => (int) ($insights['impressions'] ?? 0),
                'clicks' => (int) ($insights['clicks'] ?? 0),
                'leads' => $leads,
                'spend' => $spend,
                'cpl' => $leads > 0 ? round($spend / $leads, 2) : null,
            ]
        );
    }
}
