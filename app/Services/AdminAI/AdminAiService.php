<?php

namespace App\Services\AdminAI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminAiService
{
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->model = config('admin_ai.model', 'claude-sonnet-4-20250514');
        $this->timeout = config('admin_ai.timeout', 30);
    }

    public function processPrompt(string $prompt, $adminUser): array
    {
        $apiKey = config('services.anthropic.key');

        if (!$apiKey) {
            return [
                'intent' => 'error',
                'confidence' => 0,
                'reasoning' => 'ANTHROPIC_API_KEY is not configured.',
                'data' => [],
            ];
        }

        $systemPrompt = $this->buildSystemPrompt($adminUser);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout($this->timeout)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('AdminAiService API error', ['status' => $response->status(), 'body' => $response->body()]);
                return [
                    'intent' => 'error',
                    'confidence' => 0,
                    'reasoning' => 'API request failed with status ' . $response->status(),
                    'data' => [],
                ];
            }

            $body = $response->json();
            $text = $body['content'][0]['text'] ?? '';

            return $this->parseResponse($text);
        } catch (\Exception $e) {
            Log::error('AdminAiService exception', ['message' => $e->getMessage()]);
            return [
                'intent' => 'error',
                'confidence' => 0,
                'reasoning' => 'Exception: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    protected function buildSystemPrompt($adminUser): string
    {
        $adminName = $adminUser->full_name ?? 'Admin';

        return <<<PROMPT
Du er en AI-assistent for administrasjonen av Forfatterskolen.no (en norsk skriveskole).
Admin-brukeren heter {$adminName}.

Du skal analysere admin-forespørsler og returnere et JSON-objekt med følgende struktur:
{
  "intent": "<intent_name>",
  "confidence": <0.0-1.0>,
  "reasoning": "<kort forklaring på norsk>",
  "data": { <relevant data for the intent> }
}

Støttede intents:
1. "find_user" - Søk etter en bruker. data: {"search_term": "<navn eller e-post>", "search_type": "name|email"}
2. "get_course_overview" - Vis kursoversikt. data: {"filter": "all|active|free", "course_id": null|<id>}
3. "draft_email" - Lag et e-postutkast. data: {"to_description": "<hvem>", "subject": "<emne>", "body": "<innhold>"}
4. "create_course_draft" - Lag et kursutkast. data: {"title": "<tittel>", "description": "<beskrivelse>", "type": "<type>"}
5. "unknown" - Ukjent forespørsel. data: {"explanation": "<forklaring på norsk>"}

VIKTIG: Svar KUN med gyldig JSON. Ingen annen tekst.
PROMPT;
    }

    protected function parseResponse(string $text): array
    {
        // Strip markdown code fences if present
        $text = trim($text);
        if (str_starts_with($text, '```')) {
            $text = preg_replace('/^```(?:json)?\s*/', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);
        }

        $parsed = json_decode($text, true);

        if (!$parsed || !isset($parsed['intent'])) {
            return [
                'intent' => 'unknown',
                'confidence' => 0,
                'reasoning' => 'Kunne ikke tolke AI-svaret.',
                'data' => ['raw_response' => mb_substr($text, 0, 500)],
            ];
        }

        // Validate intent
        $validIntents = ['find_user', 'get_course_overview', 'draft_email', 'create_course_draft', 'unknown'];
        if (!in_array($parsed['intent'], $validIntents)) {
            $parsed['intent'] = 'unknown';
        }

        return [
            'intent' => $parsed['intent'],
            'confidence' => (float) ($parsed['confidence'] ?? 0),
            'reasoning' => $parsed['reasoning'] ?? '',
            'data' => $parsed['data'] ?? [],
        ];
    }
}
