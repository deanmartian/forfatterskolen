<?php

namespace App\Jobs;

use App\CoachingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SummarizeCoachingSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300];

    private CoachingSession $session;

    public function __construct(CoachingSession $session)
    {
        $this->session = $session;
    }

    public function handle(): void
    {
        if (empty($this->session->transcription)) {
            Log::warning('SummarizeCoachingSessionJob: Ingen transkripsjon funnet', [
                'session_id' => $this->session->id,
            ]);
            return;
        }

        $prompt = "Du er en assistent for Forfatterskolen, en norsk skriveskole. " .
            "Oppsummer denne veiledningssamtalen mellom redaktør og elev på norsk. " .
            "Fokuser på:\n" .
            "- Hovedtemaer som ble diskutert\n" .
            "- Tilbakemelding gitt til eleven\n" .
            "- Konkrete råd og forslag\n" .
            "- Avtalt oppfølging eller neste steg\n\n" .
            "Hold oppsummeringen konsis og strukturert med punktlister.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $this->session->transcription],
            ],
            'max_tokens' => 1000,
            'temperature' => 0.3,
        ]);

        if ($response->failed()) {
            Log::error('SummarizeCoachingSessionJob: OpenAI API feilet', [
                'session_id' => $this->session->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('OpenAI API feilet: ' . $response->body());
        }

        $data = $response->json();
        $summary = $data['choices'][0]['message']['content'] ?? '';

        $this->session->update([
            'summary' => $summary,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SummarizeCoachingSessionJob feilet', [
            'session_id' => $this->session->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
