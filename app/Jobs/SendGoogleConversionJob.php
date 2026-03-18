<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendGoogleConversionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public array $backoff = [10, 30, 60];

    public function __construct(
        protected string $conversionAction,
        protected float $value,
        protected string $orderId,
        protected string $hashedEmail,
        protected ?string $gclid = null,
    ) {}

    public function handle(): void
    {
        $customerId = config('services.google_ads.customer_id');
        $accessToken = config('services.google_ads.access_token');

        if (empty($customerId) || empty($accessToken)) {
            Log::warning('Google Ads konvertering: Mangler customer_id eller access_token');
            return;
        }

        $conversion = [
            'conversion_action' => "customers/{$customerId}/conversionActions/{$this->conversionAction}",
            'conversion_date_time' => now()->format('Y-m-d H:i:sP'),
            'conversion_value' => $this->value,
            'currency_code' => 'NOK',
            'order_id' => $this->orderId,
        ];

        if ($this->gclid) {
            $conversion['gclid'] = $this->gclid;
        }

        if ($this->hashedEmail) {
            $conversion['user_identifiers'] = [
                ['hashed_email' => $this->hashedEmail],
            ];
        }

        $response = Http::withToken($accessToken)
            ->post("https://googleads.googleapis.com/v14/customers/{$customerId}:uploadClickConversions", [
                'conversions' => [$conversion],
                'partial_failure' => true,
            ]);

        if ($response->failed()) {
            Log::error("Google Ads konvertering feilet: {$response->body()}");
            throw new \Exception("Google Ads conversion failed: {$response->status()}");
        }

        Log::info("Google Ads konvertering sendt: {$this->conversionAction}", [
            'value' => $this->value,
            'order_id' => $this->orderId,
        ]);
    }
}
