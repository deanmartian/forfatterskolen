<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use App\Services\FacebookAdsService;
use Illuminate\Console\Command;

/**
 * Master-kommando for å launche hele "Finn fortellingens motor"-
 * webinar-kampanjen i ett pass.
 *
 * Kjører:
 *   1. Verifiserer at FreeWebinar finnes (ellers oppretter den via
 *      webinar:create-motor)
 *   2. Bootstraper BigMarker + Facebook Lead Ad kald trafikk via
 *      webinar:bootstrap-integrations (hvis ikke allerede gjort)
 *   3. Oppretter HELE Meta-trakten via FacebookAdsService::
 *      createFullWebinarFunnel:
 *        - Custom Audience: website visitors 30 dager
 *        - Custom Audience: course 121 visitors 14 dager
 *        - Kald Lead Ad (3 500 kr/dag)
 *        - Retargeting webinar (1 150 kr/dag)
 *        - Retargeting kjøp med MOTOR5000 (1 000 kr/dag)
 *        - Deadline push 18-20. april (2 700 kr/dag)
 *   4. Seeder Motor-email-sekvensen via seed:motor-emails
 *   5. Printer full launch-rapport med alle IDene
 *
 * Alle kampanjer opprettes PAUSET. Sven aktiverer manuelt i Meta
 * Ads Manager når han er klar til å kjøre trafikk.
 *
 * Bruk:
 *   php artisan launch:motor-webinar
 *   php artisan launch:motor-webinar --skip-meta
 *   php artisan launch:motor-webinar --skip-emails
 *   php artisan launch:motor-webinar --webinar-id=95
 */
class LaunchMotorWebinar extends Command
{
    protected $signature = 'launch:motor-webinar
                            {--webinar-id= : FreeWebinar ID (default: finn etter tittel)}
                            {--skip-meta : Hopp over Meta-kampanje-oppretting}
                            {--skip-emails : Hopp over email-sekvens-seeding}
                            {--skip-bootstrap : Hopp over BigMarker + kald Lead Ad (hvis allerede gjort)}';

    protected $description = 'Launch "Finn fortellingens motor"-webinaret: Meta-kampanjer + email-sekvens i ett pass';

    public function handle(): int
    {
        $this->info('');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  Motor Webinar Launch');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('');

        // --- 1. Finn webinaret ---
        $webinarId = $this->option('webinar-id');
        if ($webinarId) {
            $webinar = FreeWebinar::find($webinarId);
        } else {
            $webinar = FreeWebinar::where('title', 'Finn fortellingens motor')->first();
        }

        if (!$webinar) {
            $this->error('Webinaret er ikke opprettet ennå.');
            $this->info('Kjør først: php artisan webinar:create-motor');
            return 1;
        }

        $this->info('✓ Webinar funnet:');
        $this->line('  ID:       ' . $webinar->id);
        $this->line('  Tittel:   ' . $webinar->title);
        $this->line('  Dato:     ' . $webinar->start_date);
        $this->line('  Bilde:    ' . ($webinar->image ?: '(ingen)'));
        $this->line('  BigMarker: ' . ($webinar->bigmarker_conference_id ?: '(ingen)'));
        $this->info('');

        // --- 2. Bootstrap (BigMarker + kald Lead Ad) ---
        if (!$this->option('skip-bootstrap')) {
            if (empty($webinar->bigmarker_conference_id) || empty($webinar->facebook_campaign_id)) {
                $this->info('Kjører webinar:bootstrap-integrations...');
                $skipBm = !empty($webinar->bigmarker_conference_id) ? '--skip-bigmarker' : '';
                $skipFb = !empty($webinar->facebook_campaign_id) ? '--skip-facebook' : '';
                $this->call('webinar:bootstrap-integrations', array_filter([
                    'id' => $webinar->id,
                    '--daily-budget' => 3500,
                    $skipBm,
                    $skipFb,
                ]));
                $webinar->refresh();
            } else {
                $this->info('BigMarker + kald FB Lead Ad allerede satt opp — hopper over bootstrap.');
            }
            $this->info('');
        }

        // --- 3. Meta full funnel ---
        if (!$this->option('skip-meta')) {
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('  Meta — Full Funnel (Custom Audiences + 4 kampanjer)');
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('');

            try {
                $fb = app(FacebookAdsService::class);
                $result = $fb->createFullWebinarFunnel([
                    'webinar_title' => $webinar->title,
                    'webinar_starts_at' => \Carbon\Carbon::parse($webinar->start_date),
                    'course_page' => 'https://www.forfatterskolen.no/course/121',
                    'landing_page' => route('front.free-webinar', $webinar->id),
                    'image_url' => $webinar->image ? url($webinar->image) : null,
                    'discount_code' => 'MOTOR5000',
                    'deadline_date' => \Carbon\Carbon::parse('2026-04-20 23:59:00'),
                    'budgets' => [
                        'cold_lead' => 3500,            // per Sonnets plan
                        'retargeting_webinar' => 1150,
                        'retargeting_purchase' => 1000,
                        'deadline_push' => 2700,
                    ],
                ]);

                // Custom Audiences
                $this->info('Custom Audiences:');
                foreach ($result['audiences'] as $key => $id) {
                    $this->line("  {$key}: {$id}");
                }
                $this->info('');

                // Kampanjer
                $this->info('Kampanjer (alle PAUSET):');
                foreach ($result['campaigns'] as $key => $campaign) {
                    $this->line("  {$key}:");
                    $this->line("    Campaign: " . ($campaign['campaign_id'] ?? '?'));
                    $this->line("    AdSet:    " . ($campaign['adset_id'] ?? '?'));
                    $this->line("    Ad:       " . ($campaign['ad_id'] ?? '?'));
                }
                $this->info('');

                // Feil
                if (!empty($result['errors'])) {
                    $this->warn('Noen kampanjer feilet:');
                    foreach ($result['errors'] as $key => $error) {
                        $this->warn("  {$key}: {$error}");
                    }
                    $this->info('');
                }
            } catch (\Throwable $e) {
                $this->error('Meta funnel feilet: ' . $e->getMessage());
                \Log::error('launch:motor-webinar Meta funnel feilet: ' . $e->getMessage());
            }
        }

        // --- 4. Email-sekvens ---
        if (!$this->option('skip-emails')) {
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('  Email-sekvens');
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('');
            $this->call('seed:motor-emails');
            $this->info('');
        }

        // --- 5. Launch-rapport ---
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  Launch-rapport');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('');

        $webinar->refresh();
        $this->line('Webinar:');
        $this->line('  ID:                   ' . $webinar->id);
        $this->line('  URL:                  ' . route('front.free-webinar', $webinar->id));
        $this->line('  BigMarker:            ' . ($webinar->bigmarker_conference_id ?: '(ingen)'));
        $this->line('  FB kald campaign:     ' . ($webinar->facebook_campaign_id ?: '(ingen)'));
        $this->line('  FB status:            ' . ($webinar->facebook_ad_status ?: '(ingen)'));
        $this->info('');

        $this->info('Neste steg — du må gjøre manuelt:');
        $this->info('  1. Meta Ads Manager: aktiver alle 4 kampanjer (de er PAUSET)');
        $this->info('  2. Google Ads: opprett Search- og Display-kampanjer manuelt');
        $this->info('     (copy + keywords i docs/ad-kampanjer/romankurs-webinar-launch.md)');
        $this->info('  3. Test at MOTOR5000-rabatten virker i checkout');
        $this->info('  4. Send ut mail 1 (invitasjon) til hele e-postlisten');
        $this->info('     (tekst i docs/ad-kampanjer/motor-webinar-email-sekvens.md)');
        $this->info('  5. Publiser organisk FB-post');
        $this->info('     (tekst i docs/ad-kampanjer/motor-webinar-organic-fb.md)');
        $this->info('');

        return 0;
    }
}
