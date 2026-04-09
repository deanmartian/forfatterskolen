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
            'optimization_goal' => $data['optimization_goal'] ?? 'LEAD_GENERATION',
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'promoted_object' => $data['promoted_object'] ?? json_encode(['page_id' => $this->pageId]),
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'targeting' => json_encode($data['targeting']),
            'status' => $data['status'] ?? 'PAUSED',
        ];

        return $this->request('post', "{$this->adAccountId}/adsets", $payload);
    }

    /**
     * Opprett en Website Custom Audience — brukere som har besøkt
     * spesifikke URL-er på forfatterskolen.no siste N dager.
     *
     * Eksempel: folk som var på /course/121 siste 14 dager for
     * retargeting-kampanje.
     *
     * Bruker Meta Pixel ID fra config (META_PIXEL_ID i .env).
     */
    public function createWebsiteCustomAudience(array $data): array
    {
        $pixelId = config('services.meta_pixel.id');
        if (empty($pixelId)) {
            throw new \Exception('META_PIXEL_ID må være satt for å opprette Custom Audience');
        }

        $retentionDays = (int) ($data['retention_days'] ?? 30);
        $retentionSeconds = $retentionDays * 86400;

        // Bygg rule: inklusjonsregel som matcher URL-pattern
        $filters = [];
        if (!empty($data['url_contains'])) {
            $filters[] = [
                'field' => 'url',
                'operator' => 'i_contains',
                'value' => $data['url_contains'],
            ];
        }

        $rule = [
            'inclusions' => [
                'operator' => 'or',
                'rules' => [
                    [
                        'event_sources' => [
                            ['id' => $pixelId, 'type' => 'pixel'],
                        ],
                        'retention_seconds' => $retentionSeconds,
                        'filter' => [
                            'operator' => 'and',
                            'filters' => $filters ?: [
                                ['field' => 'url', 'operator' => 'i_contains', 'value' => ''],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // NB: 'subtype' og 'pixel_id' er deprecated i Meta Marketing API v19+.
        // Modern API utleder audience-type fra selve rule-strukturen (som
        // refererer til pixelen via event_sources). Gamle parametre gir
        // "The parameter 'subtype' is not supported in the current API version"
        // (subcode 1870053).
        return $this->request('post', "{$this->adAccountId}/customaudiences", [
            'name' => $data['name'] . ' · ' . now()->format('Y-m-d H:i:s'),
            'retention_days' => $retentionDays,
            'rule' => json_encode($rule),
            'description' => $data['description'] ?? ($data['name'] ?? 'Website Custom Audience'),
        ]);
    }

    /**
     * Opprett en retargeting-kampanje som peker til en landingsside
     * (link ad, IKKE lead form). Brukes for:
     *   - Retargeting til webinar-påmelding (folk som var innom men
     *     ikke registrerte seg)
     *   - Retargeting til kurskjøp (folk som var på /course/121)
     *   - Deadline-push (siste sjanse-annonser)
     *
     * Krav: data inneholder
     *   - name: kampanjenavn
     *   - audience_id: Custom Audience ID (fra createWebsiteCustomAudience)
     *   - daily_budget: daglig budsjett i kroner
     *   - start_time, end_time: ISO 8601 datotider
     *   - landing_page: URL annonsen peker til
     *   - ad_text: primary text
     *   - ad_headline: overskrift
     *   - ad_description: beskrivelse
     *   - image_url: bilde-URL (opplastes via /adimages)
     *   - call_to_action: f.eks. 'LEARN_MORE', 'SHOP_NOW', 'SIGN_UP'
     *   - objective: default OUTCOME_TRAFFIC (kan være OUTCOME_SALES)
     */
    public function createRetargetingLinkCampaign(array $data): array
    {
        // 1. Campaign
        // NB: is_adset_budget_sharing_enabled må settes eksplisitt til
        // 'false' når vi ikke bruker campaign-level budget (vi setter
        // budsjett på adset-nivå). FB-API feiler ellers med subcode 4834011.
        $campaign = $this->createCampaign([
            'name' => $data['name'],
            'objective' => $data['objective'] ?? 'OUTCOME_TRAFFIC',
            'status' => 'PAUSED',
            'special_ad_categories' => '[]',
            'is_adset_budget_sharing_enabled' => 'false',
        ]);
        $campaignId = $campaign['id'];

        // 2. Ad Set med custom audience targeting
        $targeting = [
            'geo_locations' => ['countries' => ['NO']],
            'custom_audiences' => [['id' => $data['audience_id']]],
            'age_min' => $data['age_min'] ?? 25,
            'age_max' => $data['age_max'] ?? 65,
        ];

        // For OUTCOME_TRAFFIC: optimization_goal = LINK_CLICKS
        // For OUTCOME_SALES: optimization_goal = OFFSITE_CONVERSIONS
        $optGoal = match ($data['objective'] ?? 'OUTCOME_TRAFFIC') {
            'OUTCOME_SALES' => 'OFFSITE_CONVERSIONS',
            'OUTCOME_LEADS' => 'LEAD_GENERATION',
            default => 'LINK_CLICKS',
        };

        $adSet = $this->createAdSet([
            'campaign_id' => $campaignId,
            'name' => $data['name'],
            'daily_budget' => ((int) $data['daily_budget']) * 100, // i øre
            'start_time' => $data['start_time'] ?? now()->toIso8601String(),
            'end_time' => $data['end_time'],
            'targeting' => $targeting,
            'optimization_goal' => $optGoal,
            'promoted_object' => json_encode(['page_id' => $this->pageId]),
            'status' => 'PAUSED',
        ]);
        $adSetId = $adSet['id'];

        // 3. Creative (link ad, ikke lead form)
        $linkData = [
            'message' => $data['ad_text'],
            'name' => $data['ad_headline'],
            'description' => $data['ad_description'] ?? 'Forfatterskolen — Norges største nettbaserte skriveskole',
            'link' => $data['landing_page'],
            'call_to_action' => [
                'type' => $data['call_to_action'] ?? 'LEARN_MORE',
                'value' => ['link' => $data['landing_page']],
            ],
        ];

        // Upload bilde til /adimages hvis URL er satt
        if (!empty($data['image_url'])) {
            $imageHash = $this->uploadAdImage($data['image_url']);
            if ($imageHash) {
                $linkData['image_hash'] = $imageHash;
            }
        }

        $creative = $this->request('post', "{$this->adAccountId}/adcreatives", [
            'name' => $data['name'],
            'object_story_spec' => json_encode([
                'page_id' => $this->pageId,
                'link_data' => $linkData,
            ]),
        ]);

        // 4. Ad
        $ad = $this->request('post', "{$this->adAccountId}/ads", [
            'name' => $data['name'],
            'adset_id' => $adSetId,
            'creative' => json_encode(['creative_id' => $creative['id']]),
            'status' => 'PAUSED',
        ]);

        return [
            'campaign_id' => $campaignId,
            'adset_id' => $adSetId,
            'ad_id' => $ad['id'],
            'creative_id' => $creative['id'],
        ];
    }

    /**
     * Master-metode: opprett HELE Meta-trakten for et webinar i ett
     * API-pass. Kaller alle de individuelle metodene.
     *
     * Oppretter:
     *   1. Custom Audience: website visitors 30 dager
     *   2. Custom Audience: course page visitors 14 dager
     *   3. Kald trafikk Lead Ad-kampanje (fra før: createWebinarLeadCampaign)
     *   4. Retargeting webinar-kampanje (mot audience #1)
     *   5. Retargeting kjøp-kampanje (mot audience #2)
     *   6. Deadline-push-kampanje (18.-20. april, mot begge audiences)
     *
     * Alle kampanjer opprettes som PAUSET. Sven aktiverer manuelt i
     * Meta Ads Manager når han er klar til å kjøre trafikk.
     */
    public function createFullWebinarFunnel(array $data): array
    {
        $webinarTitle = $data['webinar_title'];
        $webinarStartsAt = $data['webinar_starts_at']; // Carbon
        $coursePage = $data['course_page'] ?? 'https://www.forfatterskolen.no/course/121';
        $landingPage = $data['landing_page']; // f.eks. /gratis-webinar/95
        $imageUrl = $data['image_url'] ?? null;
        $discountCode = $data['discount_code'] ?? 'MOTOR5000';
        $deadlineDate = $data['deadline_date'] ?? $webinarStartsAt->copy()->addDays(5); // default: 5 dager etter webinar

        $budgets = array_merge([
            'cold_lead' => 3500,
            'retargeting_webinar' => 1150,
            'retargeting_purchase' => 1000,
            'deadline_push' => 2700,
        ], $data['budgets'] ?? []);

        $result = [
            'audiences' => [],
            'campaigns' => [],
            'errors' => [],
        ];

        // === CUSTOM AUDIENCES ===
        try {
            $websiteAudience = $this->createWebsiteCustomAudience([
                'name' => "Motor Webinar · Website Visitors",
                'url_contains' => 'forfatterskolen.no',
                'retention_days' => 30,
                'description' => 'Alle som har besøkt forfatterskolen.no siste 30 dager',
            ]);
            $result['audiences']['website'] = $websiteAudience['id'];
        } catch (\Throwable $e) {
            $result['errors']['audience_website'] = $e->getMessage();
            Log::error("createWebsiteCustomAudience feilet: {$e->getMessage()}");
        }

        try {
            $courseAudience = $this->createWebsiteCustomAudience([
                'name' => "Motor Webinar · Course 121 Visitors",
                'url_contains' => '/course/121',
                'retention_days' => 14,
                'description' => 'Folk som har besøkt /course/121 siste 14 dager',
            ]);
            $result['audiences']['course'] = $courseAudience['id'];
        } catch (\Throwable $e) {
            $result['errors']['audience_course'] = $e->getMessage();
            Log::error("createWebsiteCustomAudience (course) feilet: {$e->getMessage()}");
        }

        // === 1. COLD LEAD AD (createWebinarLeadCampaign — eksisterende) ===
        // Skip hvis kald Lead Ad allerede er opprettet (f.eks. via
        // webinar:bootstrap-integrations). Unngår duplikater i FB.
        if (!empty($data['skip_cold_lead'])) {
            $result['campaigns']['cold_lead'] = ['skipped' => 'already exists'];
        } else {
            try {
                $coldLead = $this->createWebinarLeadCampaign([
                    'webinar_title' => $webinarTitle,
                    'webinar_starts_at' => $webinarStartsAt,
                    'ad_text' => $data['cold_ad_text'] ?? "Gratis webinar: {$webinarTitle}",
                    'ad_headline' => $data['cold_ad_headline'] ?? 'Meld deg på gratis webinar',
                    'daily_budget' => $budgets['cold_lead'],
                    'landing_page' => $landingPage,
                    'image_url' => $imageUrl,
                ]);
                $result['campaigns']['cold_lead'] = $coldLead;
            } catch (\Throwable $e) {
                $result['errors']['cold_lead'] = $e->getMessage();
            }
        }

        // === 2. RETARGETING WEBINAR (til landingsside) ===
        if (!empty($result['audiences']['website'])) {
            try {
                $retargetingWebinar = $this->createRetargetingLinkCampaign([
                    'name' => "Motor Retarget · Webinar-påmelding",
                    'audience_id' => $result['audiences']['website'],
                    'daily_budget' => $budgets['retargeting_webinar'],
                    'start_time' => now()->toIso8601String(),
                    'end_time' => $webinarStartsAt->toIso8601String(),
                    'landing_page' => $landingPage,
                    'objective' => 'OUTCOME_TRAFFIC',
                    'call_to_action' => 'SIGN_UP',
                    'ad_headline' => 'Du har tenkt på det en stund nå',
                    'ad_description' => "Gratis webinar {$webinarStartsAt->format('j. F')}",
                    'ad_text' => $data['retargeting_webinar_text'] ?? "Du har vært innom siden vår. Kanskje kikket på kurset. Det er ikke tilfeldig.\n\n{$webinarStartsAt->format('l j. F')} holder Kristine et gratis webinar — der hun viser deg akkurat det som holder folk tilbake fra å skrive romanen sin.\n\n60 minutter. Koster ingenting.\n\n👉 Meld deg på gratis webinar her",
                    'image_url' => $imageUrl,
                ]);
                $result['campaigns']['retargeting_webinar'] = $retargetingWebinar;
            } catch (\Throwable $e) {
                $result['errors']['retargeting_webinar'] = $e->getMessage();
            }
        }

        // === 3. RETARGETING PURCHASE (til /course/121 med MOTOR5000) ===
        if (!empty($result['audiences']['course'])) {
            try {
                $retargetingPurchase = $this->createRetargetingLinkCampaign([
                    'name' => "Motor Retarget · Kurskjøp",
                    'audience_id' => $result['audiences']['course'],
                    'daily_budget' => $budgets['retargeting_purchase'],
                    'start_time' => now()->toIso8601String(),
                    'end_time' => $deadlineDate->toIso8601String(),
                    'landing_page' => $coursePage,
                    'objective' => 'OUTCOME_TRAFFIC',
                    'call_to_action' => 'SHOP_NOW',
                    'ad_headline' => "Spar 5 000 kr — kun til {$deadlineDate->format('j. F')}",
                    'ad_description' => "Bruk kode {$discountCode} i kassen",
                    'ad_text' => $data['retargeting_purchase_text'] ?? "Du har sett Romankurs i gruppe. Nå er det én grunn til å bestemme seg:\n\n✅ 10 uker med live webinarer og kursmoduler\n✅ Profesjonell tilbakemelding på teksten din\n✅ Mentormøter med Maja Lunde, Tom Egeland m.fl.\n✅ 14 dagers angrefrist\n\nBruk kode {$discountCode} i kassen. Gjelder t.o.m. {$deadlineDate->format('j. F')}.\n\n👉 Se pakker og meld deg på",
                    'image_url' => $imageUrl,
                ]);
                $result['campaigns']['retargeting_purchase'] = $retargetingPurchase;
            } catch (\Throwable $e) {
                $result['errors']['retargeting_purchase'] = $e->getMessage();
            }
        }

        // === 4. DEADLINE-PUSH (kun siste 3 dager før deadline) ===
        $deadlineStart = $deadlineDate->copy()->subDays(2)->startOfDay();
        if (!empty($result['audiences']['website']) || !empty($result['audiences']['course'])) {
            try {
                // Kombiner begge audiences (hvis begge finnes)
                $audienceIds = array_filter([
                    $result['audiences']['website'] ?? null,
                    $result['audiences']['course'] ?? null,
                ]);
                // Bruk første audience; FB støtter flere via custom_audiences-array
                // men for enkelhet bruker vi én her
                $primaryAudience = $audienceIds[0];

                $deadlinePush = $this->createRetargetingLinkCampaign([
                    'name' => "Motor Deadline Push",
                    'audience_id' => $primaryAudience,
                    'daily_budget' => $budgets['deadline_push'],
                    'start_time' => $deadlineStart->toIso8601String(),
                    'end_time' => $deadlineDate->toIso8601String(),
                    'landing_page' => $coursePage,
                    'objective' => 'OUTCOME_TRAFFIC',
                    'call_to_action' => 'SHOP_NOW',
                    'ad_headline' => "Rabattkoden utløper ved midnatt",
                    'ad_description' => "Kursstart i dag · Siste sjanse",
                    'ad_text' => $data['deadline_text'] ?? "Romankurs i gruppe starter i dag, {$deadlineDate->format('j. F')}.\n\nWebinar-rabatten på 5 000 kr utløper ved midnatt.\n\nBruk kode {$discountCode} i kassen. 14 dagers angrefrist.\n\n👉 Meld deg på her",
                    'image_url' => $imageUrl,
                ]);
                $result['campaigns']['deadline_push'] = $deadlinePush;
            } catch (\Throwable $e) {
                $result['errors']['deadline_push'] = $e->getMessage();
            }
        }

        return $result;
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
     * Opprett Lead Form.
     *
     * Forsøker først find-or-reuse via findLeadFormByName (krever
     * Page Access Token — kan feile med #190). Hvis det ikke
     * fungerer, opprettes et nytt form med tidsstempel-suffix i
     * navnet for å unngå "Form Name already exists"-kollisjoner
     * ved retries.
     *
     * Bivirkning: orphan forms kan akkumuleres i Meta Business
     * Manager ved flere mislykkede forsøk. Det er en akseptabel
     * trade-off siden det eneste alternativet krever en Page Access
     * Token som ikke er tilgjengelig i dagens .env-oppsett.
     */
    public function createLeadForm(array $data): array
    {
        // 1. Forsøk find-or-reuse (fungerer kun med Page Access Token)
        $existing = $this->findLeadFormByName($data['name']);
        if ($existing) {
            Log::info("FB Lead Form finnes allerede, gjenbruker: {$existing['id']} ({$data['name']})");
            return $existing;
        }

        // 2. Append tidsstempel-suffix for garantert unikt navn.
        //    Sekund-presisjon, så alle realistiske retries får eget navn.
        $uniqueName = $data['name'] . ' · ' . now()->format('Y-m-d H:i:s');

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
            'name' => $uniqueName,
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
