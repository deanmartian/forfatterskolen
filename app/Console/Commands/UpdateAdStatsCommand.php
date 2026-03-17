<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use App\Services\FacebookAdsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAdStatsCommand extends Command
{
    protected $signature = 'ads:update-stats';
    protected $description = 'Oppdater annonsestatistikk fra Facebook og Google for aktive webinarer';

    public function handle()
    {
        $webinars = FreeWebinar::where('start_date', '>', now()->subDays(7))
            ->where(function ($q) {
                $q->whereNotNull('facebook_campaign_id')
                  ->orWhereNotNull('google_search_campaign_id');
            })->get();

        if ($webinars->isEmpty()) {
            $this->info('Ingen webinarer med annonser å oppdatere.');
            return 0;
        }

        foreach ($webinars as $webinar) {
            // Facebook stats
            if ($webinar->facebook_campaign_id) {
                try {
                    $fb = app(FacebookAdsService::class);
                    $stats = $fb->getCampaignStats($webinar->facebook_campaign_id);

                    if (!empty($stats['data'][0])) {
                        $data = $stats['data'][0];
                        $leads = collect($data['actions'] ?? [])
                            ->firstWhere('action_type', 'lead');

                        $webinar->update([
                            'facebook_impressions' => $data['impressions'] ?? 0,
                            'facebook_clicks' => $data['clicks'] ?? 0,
                            'facebook_spend' => $data['spend'] ?? 0,
                            'facebook_leads_count' => $leads['value'] ?? 0,
                        ]);

                        $this->info("Facebook stats oppdatert for: {$webinar->title}");
                    }
                } catch (\Exception $e) {
                    Log::warning("Facebook stats feilet for {$webinar->title}: {$e->getMessage()}");
                }
            }

            $webinar->update(['ad_stats_updated_at' => now()]);
        }

        $this->info('Annonsestatistikk oppdatert.');
        return 0;
    }
}
