<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use App\Services\BigMarkerService;
use App\Services\FacebookAdsService;
use Illuminate\Console\Command;
use Log;

/**
 * Trigger BigMarker + Facebook Lead Ad-oppsett for en eksisterende
 * FreeWebinar. Brukes når et gratiswebinar allerede er opprettet (f.eks.
 * via webinar:create-motor eller admin-form UTEN checkboxene), og vi
 * vil kjøre integrasjonene i etterkant.
 *
 * Idempotent: hvis BigMarker eller FB allerede er satt opp, skippes
 * den delen. Begge kan kjøres separat eller sammen.
 *
 * Bruk:
 *   php artisan webinar:bootstrap-integrations {id}
 *   php artisan webinar:bootstrap-integrations {id} --skip-bigmarker
 *   php artisan webinar:bootstrap-integrations {id} --skip-facebook
 *   php artisan webinar:bootstrap-integrations {id} --daily-budget=500
 */
class BootstrapWebinarIntegrations extends Command
{
    protected $signature = 'webinar:bootstrap-integrations
                            {id : FreeWebinar ID}
                            {--skip-bigmarker : Hopp over BigMarker-oppsett}
                            {--skip-facebook : Hopp over Facebook Lead Ad-oppsett}
                            {--daily-budget=500 : Daglig budsjett for FB Lead Ad (kr)}
                            {--ad-text= : Custom annonsetekst (ellers bruker webinar-beskrivelsen)}
                            {--ad-headline= : Custom overskrift (default: "Meld deg på gratis webinar")}';

    protected $description = 'Kjør BigMarker + Facebook Lead Ad-oppsett for en eksisterende FreeWebinar';

    public function handle(): int
    {
        $webinar = FreeWebinar::find($this->argument('id'));

        if (!$webinar) {
            $this->error('FreeWebinar #' . $this->argument('id') . ' ikke funnet.');
            return 1;
        }

        $this->info('');
        $this->info('Webinar funnet:');
        $this->line('  ID:       ' . $webinar->id);
        $this->line('  Tittel:   ' . $webinar->title);
        $this->line('  Dato:     ' . $webinar->start_date);
        $this->line('  Bilde:    ' . ($webinar->image ?: '(ingen)'));
        $this->info('');

        // --- BIGMARKER ---
        if (!$this->option('skip-bigmarker')) {
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('  BigMarker');
            $this->info('═══════════════════════════════════════════════════════════════');

            if (!empty($webinar->bigmarker_conference_id)) {
                $this->warn('BigMarker er allerede satt opp (ID: ' . $webinar->bigmarker_conference_id . ')');
                $this->line('Hopper over. Bruk --skip-bigmarker for å stille denne advarselen.');
                $this->info('');
            } else {
                try {
                    $bigmarker = app(BigMarkerService::class);
                    $result = $bigmarker->createConference([
                        'title' => $webinar->title,
                        'starts_at' => \Carbon\Carbon::parse($webinar->start_date),
                        'description' => strip_tags($webinar->description),
                        'duration_hours' => 1,
                    ]);

                    $conferenceId = $result['id'] ?? $result['conference_id'] ?? null;

                    if ($conferenceId) {
                        // Oppdater BEGGE ID-feltene: bigmarker_conference_id
                        // (nytt) OG gtwebinar_id (legacy). HomeController::
                        // freeWebinar leser gtwebinar_id for registrering,
                        // så uten dette feiler registrerings-flyten med 500.
                        $webinar->update([
                            'bigmarker_conference_id' => $conferenceId,
                            'gtwebinar_id' => $conferenceId,
                            'bigmarker_status' => 'active',
                        ]);

                        // Deaktiver BigMarkers egne e-poster så vi kontrollerer
                        // email-flyten selv via EmailSequence-systemet.
                        try {
                            $bigmarker->disableEmails($conferenceId);
                            $this->info('✓ BigMarker-konferanse opprettet: ' . $conferenceId);
                            $this->info('✓ BigMarker-emails deaktivert');
                        } catch (\Throwable $e) {
                            $this->warn('BigMarker-konferanse opprettet, men kunne ikke deaktivere emails: ' . $e->getMessage());
                        }
                    } else {
                        $this->error('BigMarker returnerte ingen conference ID.');
                    }
                } catch (\Throwable $e) {
                    $this->error('BigMarker feilet: ' . $e->getMessage());
                    Log::error('webinar:bootstrap-integrations BigMarker feilet for webinar ' . $webinar->id . ': ' . $e->getMessage());
                }

                $this->info('');
            }
        }

        // --- FACEBOOK LEAD AD ---
        if (!$this->option('skip-facebook')) {
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('  Facebook Lead Ad');
            $this->info('═══════════════════════════════════════════════════════════════');

            if (!empty($webinar->facebook_campaign_id)) {
                $this->warn('Facebook Lead Ad er allerede satt opp (campaign: ' . $webinar->facebook_campaign_id . ')');
                $this->line('Hopper over. Bruk --skip-facebook for å stille denne advarselen.');
                $this->info('');
            } else {
                try {
                    $adText = $this->option('ad-text') ?: strip_tags($webinar->description);
                    $adHeadline = $this->option('ad-headline') ?: 'Meld deg på gratis webinar';
                    $dailyBudget = (int) $this->option('daily-budget');

                    $fb = app(FacebookAdsService::class);
                    $fbResult = $fb->createWebinarLeadCampaign([
                        'webinar_title' => $webinar->title,
                        'webinar_starts_at' => \Carbon\Carbon::parse($webinar->start_date),
                        'ad_text' => $adText,
                        'ad_headline' => $adHeadline,
                        'daily_budget' => $dailyBudget,
                        'landing_page' => route('front.free-webinar', $webinar->id),
                        'image_url' => $webinar->image ? url($webinar->image) : null,
                    ]);

                    $webinar->update([
                        'facebook_campaign_id' => $fbResult['campaign_id'] ?? null,
                        'facebook_adset_id' => $fbResult['adset_id'] ?? null,
                        'facebook_ad_id' => $fbResult['ad_id'] ?? null,
                        'facebook_lead_form_id' => $fbResult['lead_form_id'] ?? null,
                        'facebook_ad_status' => 'paused',
                        'facebook_daily_budget' => $dailyBudget,
                        'ad_headline' => $adHeadline,
                        'ad_text' => $adText,
                    ]);

                    $this->info('✓ Facebook Lead Ad opprettet (PAUSET):');
                    $this->line('  Campaign ID:   ' . ($fbResult['campaign_id'] ?? '?'));
                    $this->line('  AdSet ID:      ' . ($fbResult['adset_id'] ?? '?'));
                    $this->line('  Ad ID:         ' . ($fbResult['ad_id'] ?? '?'));
                    $this->line('  Lead Form ID:  ' . ($fbResult['lead_form_id'] ?? '?'));
                    $this->line('  Daglig budsjett: ' . $dailyBudget . ' kr');
                    $this->info('');
                    $this->info('NB: Kampanjen er PAUSET. Aktiver manuelt i Meta Ads Manager.');
                } catch (\Throwable $e) {
                    $this->error('Facebook Lead Ad feilet: ' . $e->getMessage());
                    Log::error('webinar:bootstrap-integrations FB feilet for webinar ' . $webinar->id . ': ' . $e->getMessage());
                }

                $this->info('');
            }
        }

        // --- SUMMARY ---
        $webinar->refresh();
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  Status etter bootstrap');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->line('  BigMarker conference ID:  ' . ($webinar->bigmarker_conference_id ?: '(ingen)'));
        $this->line('  BigMarker status:         ' . ($webinar->bigmarker_status ?: '(ingen)'));
        $this->line('  Facebook campaign ID:     ' . ($webinar->facebook_campaign_id ?: '(ingen)'));
        $this->line('  Facebook ad status:       ' . ($webinar->facebook_ad_status ?: '(ingen)'));
        $this->info('');

        return 0;
    }
}
