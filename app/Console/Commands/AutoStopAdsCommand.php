<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use App\Services\FacebookAdsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoStopAdsCommand extends Command
{
    protected $signature = 'ads:auto-stop';
    protected $description = 'Stopp annonser automatisk når webinar har startet';

    public function handle()
    {
        $webinars = FreeWebinar::where('start_date', '<', now())
            ->where(function ($q) {
                $q->where('facebook_ad_status', 'active')
                  ->orWhere('google_ad_status', 'active');
            })->get();

        if ($webinars->isEmpty()) {
            $this->info('Ingen aktive annonser å stoppe.');
            return 0;
        }

        foreach ($webinars as $webinar) {
            // Stopp Facebook
            if ($webinar->facebook_ad_status === 'active' && $webinar->facebook_campaign_id) {
                try {
                    $fb = app(FacebookAdsService::class);
                    $fb->pauseCampaign(
                        $webinar->facebook_campaign_id,
                        $webinar->facebook_adset_id,
                        $webinar->facebook_ad_id
                    );
                    $webinar->update(['facebook_ad_status' => 'completed']);
                    $this->info("Facebook-annonser stoppet for: {$webinar->title}");
                } catch (\Exception $e) {
                    Log::error("Auto-stopp Facebook feilet for {$webinar->title}: {$e->getMessage()}");
                    $this->error("Facebook feil: {$e->getMessage()}");
                }
            }

            // Stopp Google (markér som completed — manuell stopp i Google Ads)
            if ($webinar->google_ad_status === 'active') {
                $webinar->update(['google_ad_status' => 'completed']);
                $this->info("Google Ads markert som fullført for: {$webinar->title}");
            }

            $this->info("Auto-stoppet annonser for: {$webinar->title}");
        }

        return 0;
    }
}
