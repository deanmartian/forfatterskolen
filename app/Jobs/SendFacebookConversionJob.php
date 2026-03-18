<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFacebookConversionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public array $backoff = [10, 30, 60];

    public function __construct(
        protected string $pixelId,
        protected string $accessToken,
        protected array $eventData,
        protected string $testEventCode = '',
    ) {}

    public function handle(): void
    {
        $payload = [
            'data' => [$this->eventData],
            'access_token' => $this->accessToken,
        ];

        if (! empty($this->testEventCode)) {
            $payload['test_event_code'] = $this->testEventCode;
        }

        $response = Http::post(
            "https://graph.facebook.com/v19.0/{$this->pixelId}/events",
            $payload
        );

        if ($response->failed()) {
            Log::error("Facebook CAPI feilet: {$response->body()}", [
                'event' => $this->eventData['event_name'] ?? 'unknown',
                'pixel_id' => $this->pixelId,
            ]);
            throw new \Exception("Facebook CAPI request failed: {$response->status()}");
        }

        Log::info("Facebook CAPI event sendt: {$this->eventData['event_name']}", [
            'event_id' => $this->eventData['event_id'] ?? null,
        ]);
    }
}
