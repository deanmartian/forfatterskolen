<?php

namespace App\Services\Helpwise;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpwiseApiService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('helpwise.base_url', 'https://api.helpwise.io'), '/');
        $this->apiKey = config('helpwise.api_key', '');
    }

    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(15);
    }

    public function getConversation(string $id): ?array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/v1/conversations/{$id}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Helpwise getConversation failed', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Helpwise getConversation exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getMessages(string $conversationId): ?array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/v1/conversations/{$conversationId}/messages");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Helpwise getMessages failed', [
                'conversation_id' => $conversationId,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Helpwise getMessages exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function createDraftReply(string $conversationId, string $body): ?array
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/v1/conversations/{$conversationId}/drafts", [
                'body' => $body,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Helpwise createDraftReply failed', [
                'conversation_id' => $conversationId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Helpwise createDraftReply exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
