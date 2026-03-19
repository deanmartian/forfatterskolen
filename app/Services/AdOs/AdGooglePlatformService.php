<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdSyncRun;
use Illuminate\Support\Facades\Log;

class AdGooglePlatformService implements AdPlatformInterface
{
    public function getPlatformName(): string
    {
        return 'google';
    }

    public function syncCampaigns(int $accountId): array
    {
        // Google Ads API integration - requires google-ads-php library
        // Currently stubbed - will be implemented when Google Ads API credentials are configured
        Log::info('AdOS Google syncCampaigns called - stub implementation', ['account_id' => $accountId]);
        return [];
    }

    public function syncMetrics(int $accountId, string $dateFrom, string $dateTo): array
    {
        Log::info('AdOS Google syncMetrics called - stub implementation', ['account_id' => $accountId]);
        return [];
    }

    public function createCampaign(array $campaignData): array
    {
        Log::info('AdOS Google createCampaign called - stub implementation', ['data' => $campaignData]);
        return ['id' => null, 'status' => 'stub'];
    }

    public function updateCampaignStatus(string $externalId, string $status): bool
    {
        Log::info('AdOS Google updateCampaignStatus called - stub', ['id' => $externalId, 'status' => $status]);
        return false;
    }

    public function updateCampaignBudget(string $externalId, float $newBudget): bool
    {
        Log::info('AdOS Google updateCampaignBudget called - stub', ['id' => $externalId, 'budget' => $newBudget]);
        return false;
    }

    public function getCampaignStats(string $externalId): array
    {
        return [];
    }
}
