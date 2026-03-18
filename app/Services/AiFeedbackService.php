<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiFeedbackService
{
    public function generateFeedback(string $lessonContent, string $questionText, string $answerText): ?string
    {
        $apiKey = config('services.anthropic.key');

        if (!$apiKey) {
            Log::warning('AiFeedbackService: ANTHROPIC_API_KEY not configured');
            return null;
        }

        // Truncate lesson content to avoid token limits
        $lessonExcerpt = mb_substr(strip_tags($lessonContent), 0, 2000);

        $systemPrompt = "Du er en erfaren skrivelærer ved Forfatterskolen. "
            . "Gi en kort, ærlig og konstruktiv tilbakemelding på elevens svar. "
            . "Vær pedagogisk — ikke for positiv, men heller ikke hard. "
            . "Maks 150 ord. Skriv på norsk.";

        $userMessage = "Leksjon:\n{$lessonExcerpt}\n\n"
            . "Oppgave:\n{$questionText}\n\n"
            . "Elevens svar:\n{$answerText}";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 500,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? null;
            }

            Log::error('AiFeedbackService: API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AiFeedbackService: Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
