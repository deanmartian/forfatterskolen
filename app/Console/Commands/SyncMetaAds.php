<?php

namespace App\Console\Commands;

use App\Services\AdOs\AdFacebookPlatformService;
use Illuminate\Console\Command;

class SyncMetaAds extends Command
{
    protected $signature = 'ads:sync-meta {--days=7 : Number of days to fetch insights for}';
    protected $description = 'Sync campaigns and metrics from Meta/Facebook Ads';

    public function handle(AdFacebookPlatformService $service): int
    {
        $days = (int) $this->option('days');

        if (empty(config('services.facebook_ads.access_token'))) {
            $this->error('FACEBOOK_ACCESS_TOKEN is not configured.');
            return 1;
        }

        // 1. Ensure ad account exists
        $this->info('Ensuring Meta ad account exists...');
        $account = $service->ensureAdAccount();
        $this->line("  Account: {$account->account_name} (ID: {$account->id})");

        // 2. Sync campaigns
        $this->info('Fetching campaigns from Meta...');
        $campaigns = $service->syncCampaigns($account->id);
        $this->line("  Synced " . count($campaigns) . " campaigns");

        if (empty($campaigns)) {
            $this->warn('No campaigns returned from Meta API. Check your access token and account ID.');
            return 0;
        }

        // Show campaign summary
        $this->table(
            ['ID', 'Name', 'Status', 'Objective'],
            collect($campaigns)->map(fn($c) => [
                $c['id'] ?? '-',
                \Illuminate\Support\Str::limit($c['name'] ?? '-', 40),
                $c['status'] ?? '-',
                $c['objective'] ?? '-',
            ])->toArray()
        );

        // 3. Sync metrics for last N days
        $dateFrom = now()->subDays($days)->toDateString();
        $dateTo = now()->toDateString();

        $this->info("Fetching insights from {$dateFrom} to {$dateTo}...");
        $metrics = $service->syncMetrics($account->id, $dateFrom, $dateTo);
        $this->line("  Synced " . count($metrics) . " metric rows");

        // 4. Summary
        $this->newLine();
        $this->info('=== Sync Summary ===');

        $totalSpend = collect($metrics)->sum(fn($m) => (float) ($m['spend'] ?? 0));
        $totalImpressions = collect($metrics)->sum(fn($m) => (int) ($m['impressions'] ?? 0));
        $totalClicks = collect($metrics)->sum(fn($m) => (int) ($m['clicks'] ?? 0));

        $totalLeads = 0;
        foreach ($metrics as $m) {
            foreach ($m['actions'] ?? [] as $action) {
                if (($action['action_type'] ?? '') === 'lead') {
                    $totalLeads += (int) ($action['value'] ?? 0);
                }
            }
        }

        $this->line("  Campaigns: " . count($campaigns));
        $this->line("  Spend (last {$days} days): " . number_format($totalSpend, 2) . " NOK");
        $this->line("  Impressions: " . number_format($totalImpressions));
        $this->line("  Clicks: " . number_format($totalClicks));
        $this->line("  Leads: " . number_format($totalLeads));

        $account->update(['last_synced_at' => now()]);

        $this->info('Meta Ads sync complete.');

        return 0;
    }
}
