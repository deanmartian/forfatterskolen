<?php

namespace App\Services\Helpwise;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpwiseApiService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.helpwise.api_key', '');
        $this->baseUrl = config('services.helpwise.base_url', 'https://app.helpwise.io/api/v1');
    }

    /**
     * Get conversation details from Helpwise API.
     */
    public function getConversation(string $conversationId): ?array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/conversations/{$conversationId}");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Helpwise API: getConversation failed', ['id' => $conversationId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get messages for a conversation.
     */
    public function getMessages(string $conversationId): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/conversations/{$conversationId}/messages");

            return $response->successful() ? ($response->json('data') ?? $response->json() ?? []) : [];
        } catch (\Exception $e) {
            Log::error('Helpwise API: getMessages failed', ['id' => $conversationId, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Create a draft reply on a conversation in Helpwise.
     * This does NOT send the email - it creates a draft for the agent to review.
     */
    public function createDraft(string $conversationId, string $body, ?string $assignTo = null): ?array
    {
        try {
            $payload = [
                'body' => $body,
                'type' => 'draft',
            ];

            if ($assignTo) {
                $payload['assign_to'] = $assignTo;
            }

            $response = Http::withHeaders($this->headers())
                ->post("{$this->baseUrl}/conversations/{$conversationId}/drafts", $payload);

            if ($response->successful()) {
                Log::info('Helpwise API: draft created', ['conversation_id' => $conversationId]);
                return $response->json();
            }

            Log::warning('Helpwise API: draft creation failed', [
                'conversation_id' => $conversationId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Helpwise API: createDraft failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Assign a conversation to a team member.
     */
    public function assignConversation(string $conversationId, string $assignTo): bool
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->put("{$this->baseUrl}/conversations/{$conversationId}/assign", [
                    'assign_to' => $assignTo,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Helpwise API: assignConversation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Add a tag to a conversation.
     */
    public function addTag(string $conversationId, string $tag): bool
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->post("{$this->baseUrl}/conversations/{$conversationId}/tags", [
                    'tag' => $tag,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Helpwise API: addTag failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
