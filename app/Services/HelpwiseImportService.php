<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpwiseImportService
{
    protected string $baseUrl = 'https://apis.helpwise.io';
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->apiKey = config('services.helpwise.api_key');
        $this->apiSecret = config('services.helpwise.api_secret') ?? env('HELPWISE_API_SECRET', '');
    }

    /**
     * Get conversations for an inbox, with pagination.
     * Uses the dev-apis endpoint which supports proper page/limit pagination.
     */
    public function getConversations(int $inboxId, ?string $pageToken = null, int $labelId = 14): array
    {
        $params = ['mailboxId' => $inboxId, 'labelId' => $labelId];
        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }

        return $this->request('GET', '/conversations', $params);
    }

    /**
     * Legacy: Get conversations using the old API (apis.helpwise.io).
     */
    public function getConversationsLegacy(int $inboxId, ?string $pageToken = null): array
    {
        $params = ['mailboxId' => $inboxId];
        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }

        return $this->request('GET', '/conversations', $params);
    }

    /**
     * Get full conversation with messages.
     */
    public function getConversationMessages(int $inboxId, string $conversationId): array
    {
        $response = $this->request('GET', "/conversations/{$conversationId}");

        return $response;
    }

    /**
     * Make an authenticated API request.
     */
    protected function request(string $method, string $endpoint, array $params = [], ?string $baseUrl = null): array
    {
        $url = ($baseUrl ?? $this->baseUrl) . $endpoint;

        // dev-apis uses raw Authorization header, old API uses Basic Auth
        if ($baseUrl && str_contains($baseUrl, 'app.helpwise.io')) {
            $request = Http::withHeaders([
                'Authorization' => $this->apiKey . ':' . $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->timeout(120);
        } else {
            $request = Http::withBasicAuth($this->apiKey, $this->apiSecret)
                ->connectTimeout(30)
                ->timeout(120)
                ->retry(3, function ($attempt, $exception) {
                    return $attempt * 10000; // 10s, 20s, 30s
                }, function ($exception, $request) {
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException
                        || ($exception instanceof \Illuminate\Http\Client\RequestException && $exception->response->status() === 429);
                }, throw: false);
        }

        try {
            if ($method === 'GET') {
                $response = $request->get($url, $params);
            } else {
                $response = $request->post($url, $params);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('HelpwiseImport: Connection timeout, retrying in 10s: ' . $e->getMessage());
            sleep(10);
            return $this->request($method, $endpoint, $params, $baseUrl);
        }

        if ($response->status() === 429) {
            Log::warning('HelpwiseImport: Rate limited, waiting 60s');
            sleep(60);
            return $this->request($method, $endpoint, $params);
        }

        if (!$response->successful()) {
            Log::error('HelpwiseImport: API error', [
                'status' => $response->status(),
                'url' => $url,
                'body' => $response->body(),
            ]);
            return [];
        }

        return $response->json() ?? [];
    }
}
