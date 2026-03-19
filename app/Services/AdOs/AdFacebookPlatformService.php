<?php

namespace App\Services\AdOs;

use App\Services\FacebookAdsService;
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
        $this->adAccountId = config('services.facebook_ads.ad_account_id', '');
    }

    public function getPlatformName(): string
    {
        return 'facebook';
    }

    public function syncCampaigns(int $accountId): array
    {
        $syncRun = AdSyncRun::start($accountId, 'facebook', 'campaigns');

        try {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/act_{$this->adAccountId}/campaigns", [
                'access_token' => $this->accessToken,
                'fields' => 'id,name,status,objective,daily_budget,lifetime_budget,start_time,stop_time',
                'limit' => 100,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Facebook API error: ' . $response->body());
            }

            $campaigns = $response->json('data', []);
            $syncRun->complete(count($campaigns), ['campaigns' => count($campaigns)]);

            return $campaigns;
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
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/act_{$this->adAccountId}/insights", [
                'access_token' => $this->accessToken,
                'fields' => 'campaign_id,campaign_name,impressions,clicks,spend,actions,cost_per_action_type,ctr,cpc',
                'level' => 'campaign',
                'time_range' => json_encode(['since' => $dateFrom, 'until' => $dateTo]),
                'time_increment' => 1,
                'limit' => 500,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Facebook API error: ' . $response->body());
            }

            $data = $response->json('data', []);
            $syncRun->complete(count($data));

            return $data;
        } catch (\Exception $e) {
            Log::error('AdOS Facebook sync metrics failed', ['error' => $e->getMessage()]);
            $syncRun->fail($e->getMessage());
            return [];
        }
    }

    public function createCampaign(array $campaignData): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$this->apiVersion}/act_{$this->adAccountId}/campaigns", [
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
                'fields' => 'impressions,clicks,spend,actions,cost_per_action_type,ctr,cpc',
                'date_preset' => 'last_7d',
            ]);

            return $response->successful() ? ($response->json('data')[0] ?? []) : [];
        } catch (\Exception $e) {
            Log::error('AdOS Facebook get stats failed', ['error' => $e->getMessage()]);
            return [];
        }
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
}
