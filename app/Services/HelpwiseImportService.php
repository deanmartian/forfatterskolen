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
     */
    public function getConversations(int $inboxId, ?string $pageToken = null): array
    {
        $params = ['mailboxId' => $inboxId];
        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }

        $response = $this->request('GET', '/conversations', $params);

        return $response;
    }

    /**
     * Get full conversation with messages.
     */
    public function getConversationMessages(int $inboxId, string $conversationId): array
    {
        $response = $this->request('GET', "/inboxes/{$inboxId}/conversations/{$conversationId}");

        return $response;
    }

    /**
     * Make an authenticated API request.
     */
    protected function request(string $method, string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $request = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->timeout(120);

        if ($method === 'GET') {
            $response = $request->get($url, $params);
        } else {
            $response = $request->post($url, $params);
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
