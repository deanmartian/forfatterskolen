<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use App\Services\FacebookAdsService;
use Illuminate\Console\Command;

/**
 * Engangs-kommando for å opprette Motor Retarget · Webinar-påmelding
 * kampanjen som manglet etter launch:motor-webinar.
 */
class CreateRetargetingWebinar extends Command
{
    protected $signature = 'motor:create-retargeting-webinar';

    protected $description = 'Opprett Motor Retarget · Webinar-påmelding (manglende kampanje)';

    public function handle(): int
    {
        $webinar = FreeWebinar::find(95);

        if (!$webinar) {
            $this->error('FreeWebinar #95 ikke funnet');
            return 1;
        }

        $this->info('Oppretter Motor Retarget · Webinar-påmelding...');

        try {
            $fb = app(FacebookAdsService::class);

            $adText = "Du har vært innom siden vår. Kanskje kikket på kurset. Det er ikke tilfeldig. 📖\n\n"
                . "Onsdag 15. april holder rektor Kristine et gratis webinar — der hun viser deg akkurat det som holder folk tilbake fra å skrive romanen sin.\n\n"
                . "60 minutter. Koster ingenting. Opptak sendes til alle påmeldte.\n\n"
                . "👉 Meld deg på gratis webinar her";

            $result = $fb->createRetargetingLinkCampaign([
                'name' => 'Motor Retarget · Webinar-påmelding',
                'audience_id' => '120241984989890619',
                'daily_budget' => 1150,
                'start_time' => now()->toIso8601String(),
                'end_time' => \Carbon\Carbon::parse('2026-04-15 20:00:00')->toIso8601String(),
                'landing_page' => 'https://www.forfatterskolen.no/gratis-webinar/95',
                'objective' => 'OUTCOME_TRAFFIC',
                'call_to_action' => 'SIGN_UP',
                'ad_headline' => 'Du har tenkt på det en stund nå',
                'ad_description' => 'Gratis webinar · 15. april kl 20:00',
                'ad_text' => $adText,
                'image_url' => $webinar->image ? url($webinar->image) : null,
            ]);

            $this->info('');
            $this->info('✓ Motor Retarget · Webinar-påmelding opprettet (PAUSET):');
            $this->line('  Campaign: ' . ($result['campaign_id'] ?? '?'));
            $this->line('  AdSet:    ' . ($result['adset_id'] ?? '?'));
            $this->line('  Ad:       ' . ($result['ad_id'] ?? '?'));
            $this->info('');
            $this->info('Neste: Gå til Meta Ads Manager → søk "Motor Retarget · Webinar"');
            $this->info('→ bytt bilde til ad5_laerere_1080.png → toggle PÅ (alle 3 nivåer)');

        } catch (\Throwable $e) {
            $this->error('Feilet: ' . $e->getMessage());
            \Log::error('motor:create-retargeting-webinar feilet: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
