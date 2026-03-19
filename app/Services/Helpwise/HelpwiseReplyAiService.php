<?php

namespace App\Services\Helpwise;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpwiseReplyAiService
{
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->model = config('admin_ai.model', 'claude-sonnet-4-20250514');
        $this->timeout = 45;
    }

    /**
     * Generate an AI reply suggestion for a support conversation.
     *
     * @param array $threadMessages  Array of message objects with 'from', 'body', 'created_at'
     * @param array|null $studentData  Enrichment data about the student (courses, status, etc.)
     * @return array|null  Parsed JSON response or null on failure
     */
    public function generateReply(array $threadMessages, ?array $studentData = null): ?array
    {
        $apiKey = config('services.anthropic.key');

        if (!$apiKey) {
            Log::error('HelpwiseReplyAiService: ANTHROPIC_API_KEY not configured');
            return null;
        }

        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt = $this->buildUserPrompt($threadMessages, $studentData);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout($this->timeout)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 2048,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('HelpwiseReplyAiService API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $body = $response->json();
            $text = $body['content'][0]['text'] ?? '';

            return $this->parseResponse($text);
        } catch (\Exception $e) {
            Log::error('HelpwiseReplyAiService exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    protected function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
Du er en vennlig og profesjonell kundeservice-assistent for Forfatterskolen.no, Norges ledende nettbaserte skriveskole.

REGLER:
- Svar ALLTID på norsk (bokmål) med mindre kunden skriver på et annet språk.
- Vær varm, hjelpsom og menneskelig i tonen.
- ALDRI dikt opp priser, kursdetaljer, datoer eller retningslinjer. Hvis du er usikker, si at du vil sjekke og komme tilbake.
- ALDRI send svaret automatisk. Du lager KUN utkast.
- Hvis henvendelsen handler om tekniske problemer, passordbytte, eller kontoendringer, foreslå å videresende til riktig avdeling.
- Hvis henvendelsen er spam, reklame, eller irrelevant, sett should_reply til false.
- Hold svarene konsise men hjelpsomme.

Du MÅ svare med gyldig JSON i følgende format (ingen tekst utenfor JSON):
{
    "language": "no",
    "should_reply": true,
    "reply_type": "support|info|escalate|spam",
    "confidence": 0.85,
    "reasoning_summary": "Kort forklaring på hvorfor du valgte dette svaret",
    "subject": "Re: Emne",
    "body": "Selve svarteksten i HTML-format"
}

reply_type verdier:
- "support": Vanlig kundeservice-svar
- "info": Informasjonsforespørsel
- "escalate": Bør eskaleres til en menneskelig medarbeider
- "spam": Spam/irrelevant melding
PROMPT;
    }

    protected function buildUserPrompt(array $threadMessages, ?array $studentData): string
    {
        $prompt = "Her er e-posttråden (nyeste melding først):\n\n";

        foreach ($threadMessages as $i => $msg) {
            $from = $msg['from'] ?? 'Ukjent';
            $date = $msg['created_at'] ?? '';
            $body = $msg['body'] ?? $msg['text'] ?? '';
            // Strip HTML tags for cleaner context
            $body = strip_tags($body);
            $prompt .= "--- Melding " . ($i + 1) . " ---\n";
            $prompt .= "Fra: {$from}\n";
            if ($date) {
                $prompt .= "Dato: {$date}\n";
            }
            $prompt .= "Innhold:\n{$body}\n\n";
        }

        if ($studentData) {
            $prompt .= "\n--- Studentinformasjon ---\n";
            if (!empty($studentData['name'])) {
                $prompt .= "Navn: {$studentData['name']}\n";
            }
            if (!empty($studentData['email'])) {
                $prompt .= "E-post: {$studentData['email']}\n";
            }
            if (!empty($studentData['courses'])) {
                $prompt .= "Kurs: " . implode(', ', $studentData['courses']) . "\n";
            }
            if (!empty($studentData['status'])) {
                $prompt .= "Status: {$studentData['status']}\n";
            }
            if (!empty($studentData['created_at'])) {
                $prompt .= "Registrert: {$studentData['created_at']}\n";
            }
        }

        $prompt .= "\nGenerer et utkast til svar basert på denne tråden. Husk å svare med gyldig JSON.";

        return $prompt;
    }

    protected function parseResponse(string $text): ?array
    {
        // Try to extract JSON from the response
        $text = trim($text);

        // Remove markdown code fences if present
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $text, $matches)) {
            $text = $matches[1];
        }

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('HelpwiseReplyAiService: failed to parse JSON', [
                'error' => json_last_error_msg(),
                'raw' => substr($text, 0, 500),
            ]);
            return null;
        }

        // Validate required fields
        $required = ['should_reply', 'confidence'];
        foreach ($required as $field) {
            if (!isset($decoded[$field])) {
                Log::warning('HelpwiseReplyAiService: missing required field', ['field' => $field]);
                return null;
            }
        }

        return $decoded;
    }
}
