<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookAdsService
{
    private string $accessToken;
    private string $adAccountId;
    private string $pageId;
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
            $error = $response->json('error.message', $response->body());
            Log::error("Facebook Ads API feil: {$endpoint}", [
                'status' => $response->status(),
                'error' => $error,
            ]);
            throw new \Exception("Facebook Ads API feil: {$error}");
        }

        return $response->json();
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
        ]);
        $campaignId = $campaign['id'];

        // 2. Opprett Lead Form
        $leadForm = $this->createLeadForm([
            'name' => "Webinar: {$data['webinar_title']}",
            'privacy_policy_url' => $data['privacy_url'] ?? 'https://forfatterskolen.no/personvern',
            'thank_you_page_url' => $data['thank_you_url'] ?? null,
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

        // 4. Opprett annonse
        $ad = $this->createLeadAd([
            'adset_id' => $adSetId,
            'name' => "Webinar Ad: {$data['webinar_title']}",
            'lead_form_id' => $leadFormId,
            'page_id' => $this->pageId,
            'image_url' => $data['image_url'] ?? null,
            'message' => $data['ad_text'] ?? "Gratis webinar: {$data['webinar_title']}",
            'headline' => $data['ad_headline'] ?? 'Meld deg på gratis webinar',
            'description' => $data['ad_description'] ?? 'Forfatterskolen — Norges største nettbaserte skriveskole',
            'link' => $data['landing_page'] ?? 'https://forfatterskolen.no',
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
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'targeting' => json_encode($data['targeting']),
            'status' => $data['status'] ?? 'PAUSED',
        ];

        return $this->request('post', "{$this->adAccountId}/adsets", $payload);
    }

    /**
     * Opprett Lead Form
     */
    public function createLeadForm(array $data): array
    {
        $questions = json_encode([
            ['type' => 'EMAIL'],
            ['type' => 'FIRST_NAME'],
            ['type' => 'LAST_NAME'],
        ]);

        $thankYou = [
            'title' => 'Takk for påmeldingen!',
            'body' => 'Vi gleder oss til å se deg på webinaret. Du vil motta en bekreftelse på e-post.',
        ];

        if (!empty($data['thank_you_page_url'])) {
            $thankYou['button_type'] = 'VIEW_WEBSITE';
            $thankYou['button_text'] = 'Gå til Forfatterskolen';
            $thankYou['website_url'] = $data['thank_you_page_url'];
        }

        return $this->request('post', "{$this->pageId}/leadgen_forms", [
            'name' => $data['name'],
            'questions' => $questions,
            'privacy_policy' => json_encode([
                'url' => $data['privacy_policy_url'],
                'link_text' => 'Personvernpolicy',
            ]),
            'thank_you_page' => json_encode($thankYou),
            'follow_up_action_url' => $data['thank_you_page_url'] ?? 'https://forfatterskolen.no',
        ]);
    }

    /**
     * Opprett Lead Ad (Creative + Ad)
     */
    public function createLeadAd(array $data): array
    {
        // Opprett creative
        $creative = $this->request('post', "{$this->adAccountId}/adcreatives", [
            'name' => $data['name'],
            'object_story_spec' => json_encode([
                'page_id' => $data['page_id'],
                'link_data' => [
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
                    'image_url' => $data['image_url'],
                ],
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
            'publisher_platforms' => ['facebook', 'instagram'],
            'facebook_positions' => ['feed', 'instant_article', 'marketplace'],
            'instagram_positions' => ['stream', 'explore'],
            'interests' => $data['interests'] ?? [
                ['id' => '6003139266461', 'name' => 'Writing'],
                ['id' => '6003384829667', 'name' => 'Books'],
                ['id' => '6003020834572', 'name' => 'Reading'],
                ['id' => '6003252462357', 'name' => 'Creative writing'],
            ],
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
     * Returnerer array med lead-data [email, first_name, last_name]
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

                // Hent lead-detaljer fra Graph API
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
