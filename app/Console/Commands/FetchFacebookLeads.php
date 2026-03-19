<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use App\Services\ContactService;
use App\WebinarRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchFacebookLeads extends Command
{
    protected $signature = 'facebook:fetch-leads {--webinar-id= : Spesifikt webinar} {--dry-run : Vis uten å registrere}';

    protected $description = 'Hent nye leads fra Facebook Lead Ads og registrer på webinar + CRM';

    public function handle(): int
    {
        $userToken = config('services.facebook_ads.access_token');
        $pageId = config('services.facebook_ads.page_id');
        $bigmarkerKey = config('services.big_marker.api_key');

        if (!$userToken || !$pageId) {
            $this->error('Facebook access token eller page ID mangler i .env');
            return 1;
        }

        // Hent Page Access Token
        $pageToken = $this->getPageToken($userToken, $pageId);
        if (!$pageToken) {
            $this->error('Kunne ikke hente Page Access Token');
            return 1;
        }

        // Finn aktive webinarer med Facebook Lead Form
        $query = FreeWebinar::whereNotNull('facebook_lead_form_id')
            ->where('facebook_lead_form_id', '!=', '')
            ->where('start_date', '>', now()->subDays(7)); // Kun webinarer siste 7 dager

        if ($this->option('webinar-id')) {
            $query->where('id', $this->option('webinar-id'));
        }

        $webinars = $query->get();

        if ($webinars->isEmpty()) {
            $this->line('Ingen aktive webinarer med Facebook Lead Form.');
            return 0;
        }

        $contactService = app(ContactService::class);
        $totalRegistered = 0;
        $totalSkipped = 0;
        $totalFailed = 0;

        foreach ($webinars as $webinar) {
            $this->info("Sjekker: {$webinar->title} (Form: {$webinar->facebook_lead_form_id})");

            // Hent leads fra Facebook
            $leads = $this->fetchLeadsFromForm($pageToken, $webinar->facebook_lead_form_id);

            if (empty($leads)) {
                $this->line("  Ingen leads.");
                continue;
            }

            $this->line("  " . count($leads) . " leads funnet.");

            foreach ($leads as $lead) {
                $fields = collect($lead['field_data'] ?? []);
                $email = strtolower(trim($fields->firstWhere('name', 'email')['values'][0] ?? ''));
                $firstName = $fields->firstWhere('name', 'first_name')['values'][0] ?? '';
                $lastName = $fields->firstWhere('name', 'last_name')['values'][0] ?? '';

                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $totalSkipped++;
                    continue;
                }

                // Sjekk om allerede registrert
                $exists = WebinarRegistration::where('free_webinar_id', $webinar->id)
                    ->where('email', $email)
                    ->exists();

                if ($exists) {
                    $totalSkipped++;
                    continue;
                }

                if ($this->option('dry-run')) {
                    $this->line("  [DRY-RUN] {$email} | {$firstName} {$lastName}");
                    $totalRegistered++;
                    continue;
                }

                try {
                    // Registrer i BigMarker
                    $joinUrl = '';
                    if ($webinar->bigmarker_conference_id) {
                        $bmr = Http::withHeaders(['API-KEY' => $bigmarkerKey])
                            ->put('https://www.bigmarker.com/api/v1/conferences/register', [
                                'id' => $webinar->bigmarker_conference_id,
                                'email' => $email,
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                            ]);
                        $joinUrl = $bmr->json('conference_url') ?? '';
                    }

                    // Lagre registrering
                    WebinarRegistration::create([
                        'free_webinar_id' => $webinar->id,
                        'email' => $email,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'join_url' => $joinUrl,
                    ]);

                    // Opprett kontakt i CRM
                    $contact = $contactService->findOrCreateByEmail($email, [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'source' => 'facebook_lead',
                    ]);
                    $contactService->tagContact($contact, 'gratis-webinar-' . $webinar->id);
                    $contactService->tagContact($contact, 'facebook-lead');

                    // Start e-postsekvens
                    try {
                        app(\App\Services\EmailAutomationService::class)->startSequence($contact, 'webinar_registration', [
                            'webinar_id' => $webinar->id,
                            'webinar_title' => $webinar->title,
                            'webinar_start_date' => $webinar->start_date,
                            'webinar_date' => \Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
                            'webinar_time' => \Carbon\Carbon::parse($webinar->start_date)->format('H:i'),
                            'join_url' => $joinUrl,
                            'replay_url' => $webinar->replay_url,
                        ]);
                    } catch (\Exception $e) {
                        // Ikke la sekvens-feil stoppe registreringen
                        Log::warning("E-postsekvens feilet for {$email}: {$e->getMessage()}");
                    }

                    $totalRegistered++;
                    $this->line("  ✅ {$email} | {$firstName} {$lastName}");

                } catch (\Exception $e) {
                    $totalFailed++;
                    $this->error("  ❌ {$email}: {$e->getMessage()}");
                    Log::error("Facebook lead registrering feilet", [
                        'email' => $email,
                        'webinar_id' => $webinar->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->newLine();
        $this->table(
            ['Registrert', 'Hoppet over', 'Feilet'],
            [[$totalRegistered, $totalSkipped, $totalFailed]]
        );

        return 0;
    }

    private function getPageToken(string $userToken, string $pageId): ?string
    {
        try {
            $r = Http::get('https://graph.facebook.com/v19.0/me/accounts', [
                'access_token' => $userToken,
            ]);

            foreach ($r->json('data', []) as $page) {
                if (($page['id'] ?? '') === $pageId) {
                    return $page['access_token'];
                }
            }
        } catch (\Exception $e) {
            Log::error("Kunne ikke hente Page Token: {$e->getMessage()}");
        }

        return null;
    }

    private function fetchLeadsFromForm(string $pageToken, string $formId): array
    {
        $allLeads = [];
        $url = "https://graph.facebook.com/v19.0/{$formId}/leads";
        $params = [
            'access_token' => $pageToken,
            'fields' => 'created_time,field_data',
            'limit' => 100,
        ];

        try {
            do {
                $r = Http::get($url, $params);
                $data = $r->json();
                $leads = $data['data'] ?? [];
                $allLeads = array_merge($allLeads, $leads);

                // Paginering
                $url = $data['paging']['next'] ?? null;
                $params = []; // next-URL har allerede params
            } while ($url && count($leads) > 0);
        } catch (\Exception $e) {
            Log::error("Kunne ikke hente leads fra form {$formId}: {$e->getMessage()}");
        }

        return $allLeads;
    }
}
