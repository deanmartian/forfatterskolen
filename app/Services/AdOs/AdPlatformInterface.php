<?php

namespace App\Services\AdOs;

interface AdPlatformInterface
{
    public function getPlatformName(): string;

    public function syncCampaigns(int $accountId): array;

    public function syncMetrics(int $accountId, string $dateFrom, string $dateTo): array;

    public function createCampaign(array $campaignData): array;

    public function updateCampaignStatus(string $externalId, string $status): bool;

    public function updateCampaignBudget(string $externalId, float $newBudget): bool;

    public function getCampaignStats(string $externalId): array;
}
