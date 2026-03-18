<?php

namespace App\Services;

use App\Jobs\SendFacebookConversionJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacebookConversionService
{
    private string $pixelId;
    private string $accessToken;
    private string $testEventCode;

    public function __construct()
    {
        $this->pixelId = config('services.facebook_ads.pixel_id', '');
        $this->accessToken = config('services.facebook_ads.capi_access_token', '');
        $this->testEventCode = config('services.facebook_ads.test_event_code', '');
    }

    /**
     * Send et konverterings-event til Facebook Conversions API (asynkront via kø)
     */
    public function sendEvent(
        string $eventName,
        array $userData = [],
        array $customData = [],
        string $sourceUrl = '',
        ?string $eventId = null,
    ): void {
        if (empty($this->pixelId) || empty($this->accessToken)) {
            Log::warning("FacebookConversionService: Pixel ID eller Access Token mangler");
            return;
        }

        $eventId = $eventId ?? Str::uuid()->toString();

        $eventData = [
            'event_name' => $eventName,
            'event_time' => time(),
            'event_id' => $eventId,
            'action_source' => 'website',
        ];

        if ($sourceUrl) {
            $eventData['event_source_url'] = $sourceUrl;
        }

        if (! empty($userData)) {
            $eventData['user_data'] = $this->hashUserData($userData);
        }

        if (! empty($customData)) {
            $eventData['custom_data'] = $customData;
        }

        SendFacebookConversionJob::dispatch(
            $this->pixelId,
            $this->accessToken,
            $eventData,
            $this->testEventCode,
        );
    }

    /**
     * Hash brukerdata med SHA-256 (per Meta-krav)
     */
    public function hashUserData(array $data): array
    {
        $hashed = [];

        if (isset($data['email'])) {
            $hashed['em'] = hash('sha256', strtolower(trim($data['email'])));
        }
        if (isset($data['first_name'])) {
            $hashed['fn'] = hash('sha256', strtolower(trim($data['first_name'])));
        }
        if (isset($data['last_name'])) {
            $hashed['ln'] = hash('sha256', strtolower(trim($data['last_name'])));
        }
        if (isset($data['phone'])) {
            $hashed['ph'] = hash('sha256', preg_replace('/[^0-9]/', '', $data['phone']));
        }

        // Ikke-hashede felt
        if (isset($data['client_ip_address'])) {
            $hashed['client_ip_address'] = $data['client_ip_address'];
        }
        if (isset($data['client_user_agent'])) {
            $hashed['client_user_agent'] = $data['client_user_agent'];
        }
        if (isset($data['fbc'])) {
            $hashed['fbc'] = $data['fbc'];
        }
        if (isset($data['fbp'])) {
            $hashed['fbp'] = $data['fbp'];
        }

        return $hashed;
    }

    // --- Convenience-metoder ---

    public function trackLead(array $userData, array $customData = [], string $sourceUrl = '', ?string $eventId = null): void
    {
        $this->sendEvent('Lead', $userData, $customData, $sourceUrl, $eventId);
    }

    public function trackPurchase(array $userData, array $purchaseData, string $sourceUrl = '', ?string $eventId = null): void
    {
        $this->sendEvent('Purchase', $userData, $purchaseData, $sourceUrl, $eventId);
    }

    public function trackViewContent(array $userData, array $contentData, string $sourceUrl = '', ?string $eventId = null): void
    {
        $this->sendEvent('ViewContent', $userData, $contentData, $sourceUrl, $eventId);
    }

    public function trackInitiateCheckout(array $userData, array $checkoutData, string $sourceUrl = '', ?string $eventId = null): void
    {
        $this->sendEvent('InitiateCheckout', $userData, $checkoutData, $sourceUrl, $eventId);
    }
}
