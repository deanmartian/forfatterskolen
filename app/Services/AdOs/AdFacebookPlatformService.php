<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdAccount;
use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdMetricSnapshot;
use App\Models\AdOs\AdSyncRun;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdFacebookPlatformService implements AdPlatformInterface
{
    private string $accessToken;
    private string $adAccountId;
    private string $apiVersion = 'v19.0';
    private string $baseUrl = 'https://graph.facebook.com';

    public function __construct()
    {
        $this->accessToken = config('services.facebook_ads.access_token', '');
        $raw = config('services.facebook_ads.ad_account_id', '');
        // Ensure we always have the act_ prefix exactly once
        $this->adAccountId = str_starts_with($raw, 'act_') ? $raw : 'act_' . $raw;
    }

    public function getPlatformName(): string
    {
        return 'facebook';
    }

    /**
     * Ensure a local AdAccount record exists for our Meta account.
     */
    public function ensureAdAccount(): AdAccount
    {
        return AdAccount::firstOrCreate(
            ['platform' => 'facebook', 'account_id' => $this->adAccountId],
            ['account_name' => 'Meta Ads', 'status' => 'active']
        );
    }

    public function syncCampaigns(int $accountId): array
    {
        $syncRun = AdSyncRun::start($accountId, 'facebook', 'campaigns');

        try {
            $allCampaigns = [];
            $url = "{$this->baseUrl}/{$this->apiVersion}/{$this->adAccountId}/campaigns";
            $params = [
                'access_token' => $this->accessToken,
                'fields' => 'id,name,status,objective,daily_budget,lifetime_budget,start_time,stop_time',
                'limit' => 100,
            ];

            // Paginate through all campaigns
            while ($url) {
                $response = Http::get($url, $params);

                if (!$response->successful()) {
                    throw new \Exception('Facebook API error: ' . $response->body());
                }

                $data = $response->json();
                $allCampaigns = array_merge($allCampaigns, $data['data'] ?? []);

                // Next page
                $url = $data['paging']['next'] ?? null;
                $params = []; // next URL already contains params
            }

            // Save campaigns to database
            foreach ($allCampaigns as $fbCampaign) {
                $this->upsertCampaign($accountId, $fbCampaign);
            }

            $syncRun->complete(count($allCampaigns), ['campaigns' => count($allCampaigns)]);

            return $allCampaigns;
        } catch (\Exception $e) {
            Log::error('AdOS Facebook sync campaigns failed', ['error' => $e->getMessage()]);
            $syncRun->fail($e->getMessage());
            return [];
        }
    }

    public function syncMetrics(int $accountId, string $dateFrom, string $dateTo): array
    {
        $syncRun = AdSyncRun::start($accountId, 'facebook', 'metrics');

        try {
            $allData = [];
            $url = "{$this->baseUrl}/{$this->apiVersion}/{$this->adAccountId}/insights";
            $params = [
                'access_token' => $this->accessToken,
                'fields' => 'campaign_id,campaign_name,impressions,clicks,spend,actions,cost_per_action_type,ctr,cpc,cpm',
                'level' => 'campaign',
                'time_range' => json_encode(['since' => $dateFrom, 'until' => $dateTo]),
                'time_increment' => 1,
                'limit' => 500,
            ];

            while ($url) {
                $response = Http::get($url, $params);

                if (!$response->successful()) {
                    throw new \Exception('Facebook API error: ' . $response->body());
                }

                $data = $response->json();
                $allData = array_merge($allData, $data['data'] ?? []);

                $url = $data['paging']['next'] ?? null;
                $params = [];
            }

            // Save metrics to database
            foreach ($allData as $row) {
                $this->upsertMetricSnapshot($accountId, $row);
            }

            $syncRun->complete(count($allData));

            return $allData;
        } catch (\Exception $e) {
            Log::error('AdOS Facebook sync metrics failed', ['error' => $e->getMessage()]);
            $syncRun->fail($e->getMessage());
            return [];
        }
    }

    public function createCampaign(array $campaignData): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$this->apiVersion}/{$this->adAccountId}/campaigns", [
                'access_token' => $this->accessToken,
                'name' => $campaignData['name'],
                'objective' => $this->mapObjective($campaignData['objective'] ?? 'leads'),
                'status' => 'PAUSED',
                'special_ad_categories' => json_encode([]),
                'daily_budget' => ($campaignData['daily_budget'] ?? 100) * 100,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Facebook campaign creation failed: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('AdOS Facebook create campaign failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateCampaignStatus(string $externalId, string $status): bool
    {
        try {
            $fbStatus = match ($status) {
                'active' => 'ACTIVE',
                'paused' => 'PAUSED',
                default => 'PAUSED',
            };

            $response = Http::post("{$this->baseUrl}/{$this->apiVersion}/{$externalId}", [
                'access_token' => $this->accessToken,
                'status' => $fbStatus,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('AdOS Facebook update status failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function updateCampaignBudget(string $externalId, float $newBudget): bool
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$this->apiVersion}/{$externalId}", [
                'access_token' => $this->accessToken,
                'daily_budget' => (int) ($newBudget * 100),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('AdOS Facebook update budget failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function getCampaignStats(string $externalId): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/{$externalId}/insights", [
                'access_token' => $this->accessToken,
                'fields' => 'impressions,clicks,spend,actions,cost_per_action_type,ctr,cpc,cpm',
                'date_preset' => 'last_7d',
            ]);

            return $response->successful() ? ($response->json('data')[0] ?? []) : [];
        } catch (\Exception $e) {
            Log::error('AdOS Facebook get stats failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Upsert a campaign from Facebook API data into the local ad_campaigns table.
     */
    private function upsertCampaign(int $accountId, array $fbCampaign): AdCampaign
    {
        $status = $this->mapFbStatus($fbCampaign['status'] ?? 'PAUSED');
        $objective = $this->mapFbObjective($fbCampaign['objective'] ?? '');

        // Budget values from Meta are in cents/oere - divide by 100
        $dailyBudget = isset($fbCampaign['daily_budget']) ? (float) $fbCampaign['daily_budget'] / 100 : null;
        $totalBudget = isset($fbCampaign['lifetime_budget']) ? (float) $fbCampaign['lifetime_budget'] / 100 : null;

        return AdCampaign::updateOrCreate(
            ['external_id' => $fbCampaign['id'], 'platform' => 'facebook'],
            [
                'account_id' => $accountId,
                'name' => $fbCampaign['name'],
                'objective' => $objective,
                'status' => $status,
                'daily_budget' => $dailyBudget,
                'total_budget' => $totalBudget,
                'platform_meta' => $fbCampaign,
            ]
        );
    }

    /**
     * Upsert a metric snapshot from Facebook insights data.
     */
    private function upsertMetricSnapshot(int $accountId, array $row): void
    {
        $campaignExternalId = $row['campaign_id'] ?? null;
        if (!$campaignExternalId) {
            return;
        }

        $campaign = AdCampaign::where('external_id', $campaignExternalId)
            ->where('platform', 'facebook')
            ->first();

        if (!$campaign) {
            return;
        }

        // Extract leads and conversions from actions array
        $leads = 0;
        $conversions = 0;
        foreach ($row['actions'] ?? [] as $action) {
            if (($action['action_type'] ?? '') === 'lead') {
                $leads += (int) ($action['value'] ?? 0);
            }
            if (in_array($action['action_type'] ?? '', ['lead', 'offsite_conversion.fb_pixel_lead', 'offsite_conversion.fb_pixel_purchase', 'purchase', 'complete_registration'])) {
                $conversions += (int) ($action['value'] ?? 0);
            }
        }

        // Spend from Meta is already in the account currency (NOK), not in cents for insights
        $spend = (float) ($row['spend'] ?? 0);
        $impressions = (int) ($row['impressions'] ?? 0);
        $clicks = (int) ($row['clicks'] ?? 0);
        $ctr = (float) ($row['ctr'] ?? 0);
        $cpc = (float) ($row['cpc'] ?? 0);
        $cpm = (float) ($row['cpm'] ?? 0);

        $cpa = $conversions > 0 ? $spend / $conversions : null;

        $date = $row['date_start'] ?? now()->toDateString();

        AdMetricSnapshot::updateOrCreate(
            [
                'level' => 'campaign',
                'reference_id' => $campaign->id,
                'campaign_id' => $campaign->id,
                'date' => $date,
            ],
            [
                'impressions' => $impressions,
                'clicks' => $clicks,
                'spend' => $spend,
                'conversions' => $conversions,
                'leads' => $leads,
                'cpa' => $cpa,
                'ctr' => $ctr,
                'cpc' => $cpc,
                'cpm' => $cpm,
                'platform_data' => $row,
            ]
        );

        // Update campaign spent_total
        $totalSpent = AdMetricSnapshot::where('campaign_id', $campaign->id)
            ->where('level', 'campaign')
            ->sum('spend');
        $campaign->update(['spent_total' => $totalSpent]);
    }

    private function mapObjective(string $objective): string
    {
        return match ($objective) {
            'leads' => 'OUTCOME_LEADS',
            'conversions', 'sales' => 'OUTCOME_SALES',
            'traffic' => 'OUTCOME_TRAFFIC',
            'awareness' => 'OUTCOME_AWARENESS',
            'engagement' => 'OUTCOME_ENGAGEMENT',
            default => 'OUTCOME_LEADS',
        };
    }

    private function mapFbStatus(string $fbStatus): string
    {
        return match (strtoupper($fbStatus)) {
            'ACTIVE' => 'active',
            'PAUSED' => 'paused',
            'ARCHIVED' => 'archived',
            'DELETED' => 'archived',
            default => 'paused',
        };
    }

    private function mapFbObjective(string $fbObjective): string
    {
        return match (strtoupper($fbObjective)) {
            'OUTCOME_LEADS', 'LEAD_GENERATION' => 'leads',
            'OUTCOME_SALES', 'CONVERSIONS' => 'conversions',
            'OUTCOME_TRAFFIC', 'LINK_CLICKS' => 'traffic',
            'OUTCOME_AWARENESS', 'BRAND_AWARENESS', 'REACH' => 'awareness',
            'OUTCOME_ENGAGEMENT', 'POST_ENGAGEMENT', 'PAGE_LIKES' => 'engagement',
            'OUTCOME_APP_PROMOTION', 'APP_INSTALLS' => 'app_installs',
            default => 'leads',
        };
    }
}
