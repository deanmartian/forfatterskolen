<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookAdsService
{
    private string|null $accessToken = null;
    private string|null $adAccountId = null;
    private string|null $pageId = null;
    private string $baseUrl = 'https://graph.facebook.com/v19.0';

    public function __construct()
    {
        $this->accessToken = config('services.facebook_ads.access_token');
        $this->adAccountId = config('services.facebook_ads.ad_account_id');
        $this->pageId = config('services.facebook_ads.page_id');
    }

    /**
     * Generisk Graph API-kall
     */
    private function request(string $method, string $endpoint, array $data = [])
    {
        $data['access_token'] = $this->accessToken;

        $response = Http::{$method}("{$this->baseUrl}/{$endpoint}", $data);

        if (!$response->successful()) {
            // Full error-detaljer fra Facebook — viktig for debugging.
            // FB returnerer vanligvis: message, type, code, error_subcode,
            // error_user_title, error_user_msg, fbtrace_id
            $errorJson = $response->json('error', []);
            $message = $errorJson['message'] ?? 'Ukjent feil';
            $userMsg = $errorJson['error_user_msg'] ?? null;
            $userTitle = $errorJson['error_user_title'] ?? null;
            $subcode = $errorJson['error_subcode'] ?? null;
            $traceId = $errorJson['fbtrace_id'] ?? null;

            // Hvis vi har en user_msg, inkluder den i feilen — mye mer
            // informativ enn bare "Invalid parameter"
            $fullMessage = $message;
            if ($userTitle) $fullMessage .= " [{$userTitle}]";
            if ($userMsg) $fullMessage .= ": {$userMsg}";
            if ($subcode) $fullMessage .= " (subcode: {$subcode})";

            Log::error("Facebook Ads API feil: {$endpoint}", [
                'status' => $response->status(),
                'message' => $message,
                'user_title' => $userTitle,
                'user_msg' => $userMsg,
                'subcode' => $subcode,
                'trace_id' => $traceId,
                'full_response' => $errorJson,
                'request_payload' => array_diff_key($data, ['access_token' => '']),
            ]);

            throw new \Exception("Facebook Ads API feil: {$fullMessage}");
        }

        return $response->json();
    }

    /**
     * Last opp et bilde til Facebook Ads (/act_<id>/adimages) og returnér
     * image_hash. Dette er den korrekte måten å inkludere bilder i
     * adcreatives — direkte image_url i link_data fungerer ikke
     * pålitelig i moderne Marketing API-versjoner.
     *
     * Returnerer null hvis opplasting feiler — kaller kan da velge å
     * opprette annonsen uten bilde eller avbryte.
     */
    public function uploadAdImage(string $imageUrl): ?string
    {
        try {
            // Last ned bildet først
            $imageResponse = Http::timeout(15)->get($imageUrl);
            if (!$imageResponse->successful()) {
                Log::warning("Kunne ikke hente bilde fra {$imageUrl}: status " . $imageResponse->status());
                return null;
            }

            $imageBytes = $imageResponse->body();
            $extension = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg');
            $filename = 'ad-' . uniqid() . '.' . $extension;

            // POST til /adimages med multipart
            $response = Http::attach('file', $imageBytes, $filename)
                ->post("{$this->baseUrl}/{$this->adAccountId}/adimages", [
                    'access_token' => $this->accessToken,
                ]);

            if (!$response->successful()) {
                $error = $response->json('error.message', $response->body());
                Log::error("Facebook /adimages opplasting feilet: {$error}", [
                    'status' => $response->status(),
                    'url' => $imageUrl,
                ]);
                return null;
            }

            // FB returnerer: { "images": { "<filename>": { "hash": "...", "url": "..." } } }
            $images = $response->json('images', []);
            foreach ($images as $image) {
                if (!empty($image['hash'])) {
                    return $image['hash'];
                }
            }

            Log::warning("Facebook /adimages returnerte ingen hash", [
                'response' => $response->json(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error("uploadAdImage exception: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Opprett komplett Lead Ad-kampanje for webinar
     */
    public function createWebinarLeadCampaign(array $data): array
    {
        // 1. Opprett kampanje
        $campaign = $this->createCampaign([
            'name' => "Webinar Lead: {$data['webinar_title']}",
            'objective' => 'OUTCOME_LEADS',
            'status' => 'PAUSED',
            'special_ad_categories' => '[]',
            'is_adset_budget_sharing_enabled' => 'false',
        ]);
        $campaignId = $campaign['id'];

        // 2. Opprett Lead Form
        $leadForm = $this->createLeadForm([
            'name' => "Webinar: {$data['webinar_title']}",
            'privacy_policy_url' => $data['privacy_url'] ?? 'https://www.forfatterskolen.no/terms/all',
            'thank_you_page_url' => $data['thank_you_url'] ?? $data['landing_page'] ?? 'https://www.forfatterskolen.no',
        ]);
        $leadFormId = $leadForm['id'];

        // 3. Opprett Ad Set med targeting
        $adSet = $this->createAdSet([
            'campaign_id' => $campaignId,
            'name' => "Webinar: {$data['webinar_title']}",
            'daily_budget' => ($data['daily_budget'] ?? 200) * 100, // i øre
            'start_time' => now()->toIso8601String(),
            'end_time' => $data['webinar_starts_at']->toIso8601String(),
            'targeting' => $this->getDefaultTargeting($data),
            'status' => 'PAUSED',
        ]);
        $adSetId = $adSet['id'];

        // 4. Sett destination_type til ON_AD for lead forms
        $this->request('post', $adSetId, ['destination_type' => 'ON_AD']);

        // 5. Opprett annonse
        $ad = $this->createLeadAd([
            'adset_id' => $adSetId,
            'name' => "Webinar Ad: {$data['webinar_title']}",
            'lead_form_id' => $leadFormId,
            'page_id' => $this->pageId,
            'image_url' => $data['image_url'] ?? null,
            'message' => $data['ad_text'] ?? "Gratis webinar: {$data['webinar_title']}",
            'headline' => $data['ad_headline'] ?? 'Meld deg på gratis webinar',
            'description' => $data['ad_description'] ?? 'Forfatterskolen — Norges største nettbaserte skriveskole',
            'link' => $data['landing_page'] ?? 'https://www.forfatterskolen.no',
            'call_to_action' => 'SIGN_UP',
        ]);

        return [
            'campaign_id' => $campaignId,
            'adset_id' => $adSetId,
            'ad_id' => $ad['id'],
            'lead_form_id' => $leadFormId,
        ];
    }

    /**
     * Opprett kampanje
     */
    public function createCampaign(array $data): array
    {
        return $this->request('post', "{$this->adAccountId}/campaigns", $data);
    }

    /**
     * Opprett Ad Set
     */
    public function createAdSet(array $data): array
    {
        $payload = [
            'campaign_id' => $data['campaign_id'],
            'name' => $data['name'],
            'daily_budget' => $data['daily_budget'],
            'billing_event' => 'IMPRESSIONS',
            'optimization_goal' => 'LEAD_GENERATION',
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'promoted_object' => json_encode(['page_id' => $this->pageId]),
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'targeting' => json_encode($data['targeting']),
            'status' => $data['status'] ?? 'PAUSED',
        ];

        return $this->request('post', "{$this->adAccountId}/adsets", $payload);
    }

    /**
     * Finn et eksisterende Lead Form på siden ved navn.
     * Returnerer null hvis ingen matcher. Brukes av createLeadForm
     * for å unngå "Form Name already exists"-feil ved retries.
     */
    public function findLeadFormByName(string $name): ?array
    {
        try {
            $response = $this->request('get', "{$this->pageId}/leadgen_forms", [
                'fields' => 'id,name,status',
                'limit' => 100,
            ]);

            foreach ($response['data'] ?? [] as $form) {
                if (($form['name'] ?? null) === $name) {
                    return $form;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("findLeadFormByName feilet: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Opprett Lead Form — eller returner eksisterende hvis navnet
     * allerede finnes på siden. Dette gjør createWebinarLeadCampaign
     * trygt å kjøre på nytt ved feil, uten å ende opp med duplikat-
     * former eller "Form Name already exists"-error.
     */
    public function createLeadForm(array $data): array
    {
        // 1. Sjekk om et form med samme navn allerede eksisterer (retry-case)
        $existing = $this->findLeadFormByName($data['name']);
        if ($existing) {
            Log::info("FB Lead Form finnes allerede, gjenbruker: {$existing['id']} ({$data['name']})");
            return $existing;
        }

        // 2. Ellers opprett nytt
        $questions = json_encode([
            ['type' => 'EMAIL'],
            ['type' => 'FIRST_NAME'],
            ['type' => 'LAST_NAME'],
        ]);

        $thankYou = [
            'title' => 'Takk for påmeldingen!',
            'body' => 'Vi gleder oss til å se deg på webinaret. Du vil motta en bekreftelse på e-post.',
            'button_type' => 'VIEW_WEBSITE',
            'button_text' => 'Gå til Forfatterskolen',
            'website_url' => $data['thank_you_page_url'] ?? 'https://www.forfatterskolen.no',
        ];

        return $this->request('post', "{$this->pageId}/leadgen_forms", [
            'name' => $data['name'],
            'questions' => $questions,
            'privacy_policy' => json_encode([
                'url' => $data['privacy_policy_url'],
                'link_text' => 'Personvernpolicy',
            ]),
            'thank_you_page' => json_encode($thankYou),
            'follow_up_action_url' => $data['thank_you_page_url'] ?? 'https://www.forfatterskolen.no',
        ]);
    }

    /**
     * Opprett Lead Ad (Creative + Ad).
     *
     * Hvis image_url er satt, lastes bildet først opp via
     * uploadAdImage() → image_hash, og image_hash brukes i link_data
     * (ikke image_url direkte). Dette er den korrekte måten å
     * inkludere bilder i FB Marketing API — direkte image_url
     * fungerer ikke pålitelig og gir "Invalid parameter".
     *
     * Hvis bilde-opplasting feiler ELLER image_url ikke er satt,
     * opprettes annonsen uten bilde. FB tillater tekst-only annonser,
     * selv om de performer dårligere.
     */
    public function createLeadAd(array $data): array
    {
        $linkData = [
            'message' => $data['message'],
            'name' => $data['headline'],
            'description' => $data['description'],
            'link' => $data['link'],
            'call_to_action' => [
                'type' => $data['call_to_action'],
                'value' => [
                    'lead_gen_form_id' => $data['lead_form_id'],
                ],
            ],
        ];

        // Last opp bilde til /adimages hvis image_url er satt
        if (!empty($data['image_url'])) {
            $imageHash = $this->uploadAdImage($data['image_url']);
            if ($imageHash) {
                $linkData['image_hash'] = $imageHash;
                Log::info("FB Lead Ad: bilde opplastet, hash = {$imageHash}");
            } else {
                Log::warning("FB Lead Ad: bilde-opplasting feilet, oppretter annonse uten bilde");
            }
        }

        // Opprett creative
        $creative = $this->request('post', "{$this->adAccountId}/adcreatives", [
            'name' => $data['name'],
            'object_story_spec' => json_encode([
                'page_id' => $data['page_id'],
                'link_data' => $linkData,
            ]),
        ]);

        // Opprett selve annonsen
        return $this->request('post', "{$this->adAccountId}/ads", [
            'name' => $data['name'],
            'adset_id' => $data['adset_id'],
            'creative' => json_encode(['creative_id' => $creative['id']]),
            'status' => 'PAUSED',
        ]);
    }

    /**
     * Standard targeting for Forfatterskolens webinarer
     */
    private function getDefaultTargeting(array $data = []): array
    {
        return [
            'geo_locations' => [
                'countries' => ['NO'],
            ],
            'age_min' => $data['age_min'] ?? 25,
            'age_max' => $data['age_max'] ?? 65,
        ];
    }

    /**
     * Hent leads fra et Lead Form
     */
    public function getLeads(string $formId, int $limit = 50): array
    {
        return $this->request('get', "{$formId}/leads", [
            'limit' => $limit,
            'fields' => 'created_time,field_data',
        ]);
    }

    /**
     * Hent kampanjestatistikk
     */
    public function getCampaignStats(string $campaignId): array
    {
        return $this->request('get', "{$campaignId}/insights", [
            'fields' => 'impressions,clicks,spend,actions,cost_per_action_type',
            'date_preset' => 'lifetime',
        ]);
    }

    /**
     * Aktiver kampanje (sett ACTIVE på campaign, adset, ad)
     */
    public function activateCampaign(string $campaignId, string $adSetId = null, string $adId = null): void
    {
        $this->request('post', $campaignId, ['status' => 'ACTIVE']);
        if ($adSetId) {
            $this->request('post', $adSetId, ['status' => 'ACTIVE']);
        }
        if ($adId) {
            $this->request('post', $adId, ['status' => 'ACTIVE']);
        }
    }

    /**
     * Pause kampanje
     */
    public function pauseCampaign(string $campaignId, string $adSetId = null, string $adId = null): void
    {
        $this->request('post', $campaignId, ['status' => 'PAUSED']);
        if ($adSetId) {
            $this->request('post', $adSetId, ['status' => 'PAUSED']);
        }
        if ($adId) {
            $this->request('post', $adId, ['status' => 'PAUSED']);
        }
    }

    /**
     * Verifiser webhook (GET-forespørsel fra Facebook)
     */
    public static function verifyWebhook($request): ?string
    {
        $verifyToken = config('services.facebook_ads.webhook_verify_token');

        if ($request->input('hub_mode') === 'subscribe'
            && $request->input('hub_verify_token') === $verifyToken) {
            return $request->input('hub_challenge');
        }

        return null;
    }

    /**
     * Prosesser webhook-data fra Facebook Lead Ads
     */
    public static function parseLeadWebhook(array $payload): array
    {
        $leads = [];

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? '') !== 'leadgen') {
                    continue;
                }

                $leadgenId = $change['value']['leadgen_id'] ?? null;
                if (!$leadgenId) {
                    continue;
                }

                try {
                    $service = app(self::class);
                    $leadData = $service->request('get', $leadgenId, [
                        'fields' => 'field_data,created_time',
                    ]);

                    $lead = [
                        'leadgen_id' => $leadgenId,
                        'form_id' => $change['value']['form_id'] ?? null,
                        'created_time' => $leadData['created_time'] ?? null,
                    ];

                    foreach ($leadData['field_data'] ?? [] as $field) {
                        $name = strtolower($field['name'] ?? '');
                        $value = $field['values'][0] ?? '';

                        if ($name === 'email') $lead['email'] = $value;
                        if ($name === 'first_name') $lead['first_name'] = $value;
                        if ($name === 'last_name') $lead['last_name'] = $value;
                    }

                    if (!empty($lead['email'])) {
                        $leads[] = $lead;
                    }
                } catch (\Exception $e) {
                    Log::error("Facebook Lead webhook feil for lead {$leadgenId}: {$e->getMessage()}");
                }
            }
        }

        return $leads;
    }
}
