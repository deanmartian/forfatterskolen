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

    /**
     * Create a full campaign with ad set, creative, and ad in one call.
     * All campaigns start PAUSED for safety.
     */
    public function createFullCampaign(array $config): array
    {
        $pageId = config('services.facebook_ads.page_id', env('FACEBOOK_PAGE_ID'));
        $results = ['steps' => []];

        try {
            // 1. Create Campaign (always PAUSED)
            $objective = $config['objective'] ?? 'OUTCOME_LEADS';
            // If short form like 'leads', map it
            if (!str_starts_with($objective, 'OUTCOME_')) {
                $objective = $this->mapObjective($objective);
            }

            $campaignParams = [
                'access_token' => $this->accessToken,
                'name' => $config['name'] ?? 'Ny kampanje',
                'objective' => $objective,
                'status' => 'PAUSED',
                'special_ad_categories' => json_encode([]),
            ];

            Log::info('AdOS creating Facebook campaign', $campaignParams);
            $campaignResponse = Http::asForm()->post(
                "{$this->baseUrl}/{$this->apiVersion}/{$this->adAccountId}/campaigns",
                $campaignParams
            );

            if (!$campaignResponse->successful()) {
                throw new \Exception('Campaign creation failed: ' . $campaignResponse->body());
            }

            $campaignId = $campaignResponse->json('id');
            $results['campaign_id'] = $campaignId;
            $results['steps'][] = ['step' => 'campaign', 'id' => $campaignId, 'status' => 'ok'];

            // 2. Create Ad Set
            $dailyBudgetOre = (int) (($config['daily_budget'] ?? 150) * 100);
            $targeting = $this->buildTargeting($config['targeting'] ?? []);

            $adSetParams = [
                'access_token' => $this->accessToken,
                'campaign_id' => $campaignId,
                'name' => ($config['name'] ?? 'Ny kampanje') . ' - Ad Set',
                'status' => 'PAUSED',
                'daily_budget' => $dailyBudgetOre,
                'billing_event' => 'IMPRESSIONS',
                'optimization_goal' => $this->mapOptimizationGoal($objective),
                'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
                'targeting' => json_encode($targeting),
                'start_time' => now()->addHour()->toIso8601String(),
            ];

            // promoted_object for leads objective
            if (in_array($objective, ['OUTCOME_LEADS', 'OUTCOME_SALES'])) {
                $adSetParams['promoted_object'] = json_encode(['page_id' => $pageId]);
            }

            Log::info('AdOS creating Facebook ad set', ['campaign_id' => $campaignId]);
            $adSetResponse = Http::asForm()->post(
                "{$this->baseUrl}/{$this->apiVersion}/{$this->adAccountId}/adsets",
                $adSetParams
            );

            if (!$adSetResponse->successful()) {
                throw new \Exception('Ad set creation failed: ' . $adSetResponse->body());
            }

            $adSetId = $adSetResponse->json('id');
            $results['adset_id'] = $adSetId;
            $results['steps'][] = ['step' => 'adset', 'id' => $adSetId, 'status' => 'ok'];

            // 3. Create Ad Creative
            $creative = $config['creative'] ?? [];
            $linkData = [
                'message' => $creative['message'] ?? 'Lær å skrive med Forfatterskolen',
                'link' => $creative['link'] ?? 'https://forfratterskolen.no',
                'name' => $creative['headline'] ?? 'Forfatterskolen',
                'description' => $creative['description'] ?? '',
                'call_to_action' => [
                    'type' => $creative['call_to_action'] ?? 'SIGN_UP',
                    'value' => ['link' => $creative['link'] ?? 'https://forfatterskolen.no'],
                ],
            ];

            if (!empty($creative['image_url'])) {
                $linkData['picture'] = $creative['image_url'];
            }

            $creativeParams = [
                'access_token' => $this->accessToken,
                'name' => ($config['name'] ?? 'Ny kampanje') . ' - Creative',
                'object_story_spec' => json_encode([
                    'page_id' => $pageId,
                    'link_data' => $linkData,
                ]),
            ];

            Log::info('AdOS creating Facebook ad creative', ['campaign_id' => $campaignId]);
            $creativeResponse = Http::asForm()->post(
                "{$this->baseUrl}/{$this->apiVersion}/{$this->adAccountId}/adcreatives",
                $creativeParams
            );

            if (!$creativeResponse->successful()) {
                throw new \Exception('Creative creation failed: ' . $creativeResponse->body());
            }

            $creativeId = $creativeResponse->json('id');
            $results['creative_id'] = $creativeId;
            $results['steps'][] = ['step' => 'creative', 'id' => $creativeId, 'status' => 'ok'];

            // 4. Create Ad
            $adParams = [
                'access_token' => $this->accessToken,
                'name' => ($config['name'] ?? 'Ny kampanje') . ' - Ad',
                'adset_id' => $adSetId,
                'creative' => json_encode(['creative_id' => $creativeId]),
                'status' => 'PAUSED',
            ];

            Log::info('AdOS creating Facebook ad', ['adset_id' => $adSetId]);
            $adResponse = Http::asForm()->post(
                "{$this->baseUrl}/{$this->apiVersion}/{$this->adAccountId}/ads",
                $adParams
            );

            if (!$adResponse->successful()) {
                throw new \Exception('Ad creation failed: ' . $adResponse->body());
            }

            $adId = $adResponse->json('id');
            $results['ad_id'] = $adId;
            $results['steps'][] = ['step' => 'ad', 'id' => $adId, 'status' => 'ok'];
            $results['success'] = true;

            Log::info('AdOS full campaign created successfully', [
                'campaign_id' => $campaignId,
                'adset_id' => $adSetId,
                'creative_id' => $creativeId,
                'ad_id' => $adId,
            ]);

            return $results;

        } catch (\Exception $e) {
            Log::error('AdOS createFullCampaign failed', [
                'error' => $e->getMessage(),
                'steps_completed' => $results['steps'],
            ]);
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            return $results;
        }
    }

    /**
     * Pause a campaign by external ID.
     */
    public function pauseCampaign(string $campaignId): bool
    {
        return $this->updateCampaignStatus($campaignId, 'paused');
    }

    /**
     * Resume/activate a campaign by external ID.
     */
    public function resumeCampaign(string $campaignId): bool
    {
        return $this->updateCampaignStatus($campaignId, 'active');
    }

    /**
     * Get campaign performance insights for a given number of days.
     */
    public function getCampaignInsights(string $campaignId, int $days = 7): array
    {
        try {
            $since = now()->subDays($days)->format('Y-m-d');
            $until = now()->format('Y-m-d');

            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/{$campaignId}/insights", [
                'access_token' => $this->accessToken,
                'fields' => 'impressions,clicks,spend,actions,cost_per_action_type,ctr,cpc,cpm,reach,frequency',
                'time_range' => json_encode(['since' => $since, 'until' => $until]),
                'time_increment' => 1,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Insights fetch failed: ' . $response->body());
            }

            return $response->json('data', []);
        } catch (\Exception $e) {
            Log::error('AdOS getCampaignInsights failed', ['campaign_id' => $campaignId, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Build Meta targeting spec from config.
     */
    private function buildTargeting(array $config): array
    {
        $targeting = [
            'geo_locations' => [
                'countries' => $config['countries'] ?? ['NO'],
            ],
            'age_min' => $config['age_min'] ?? 25,
            'age_max' => $config['age_max'] ?? 65,
            'targeting_automation' => [
                'advantage_audience' => 0,
            ],
        ];

        if (!empty($config['genders']) && $config['genders'] !== [0]) {
            $targeting['genders'] = $config['genders'];
        }

        if (!empty($config['interests'])) {
            $targeting['flexible_spec'] = [
                ['interests' => $config['interests']],
            ];
        }

        if (!empty($config['custom_audiences'])) {
            $targeting['custom_audiences'] = array_map(fn($id) => ['id' => $id], $config['custom_audiences']);
        }

        return $targeting;
    }

    /**
     * Map campaign objective to ad set optimization goal.
     */
    private function mapOptimizationGoal(string $objective): string
    {
        return match ($objective) {
            'OUTCOME_LEADS' => 'LEAD_GENERATION',
            'OUTCOME_SALES' => 'OFFSITE_CONVERSIONS',
            'OUTCOME_TRAFFIC' => 'LINK_CLICKS',
            'OUTCOME_AWARENESS' => 'REACH',
            'OUTCOME_ENGAGEMENT' => 'POST_ENGAGEMENT',
            default => 'LEAD_GENERATION',
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
