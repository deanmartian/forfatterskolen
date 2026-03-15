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
use Illuminate\Support\Facades\Storage;

class TranscribeCoachingSessionJob implements ShouldQueue
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
        $filePath = Storage::disk('local')->path($this->session->recording_path);

        if (!file_exists($filePath)) {
            Log::error('TranscribeCoachingSessionJob: Lydfil ikke funnet', [
                'session_id' => $this->session->id,
                'path' => $filePath,
            ]);
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
        ])->attach(
            'file', file_get_contents($filePath), basename($filePath)
        )->post('https://api.openai.com/v1/audio/transcriptions', [
            'model' => 'whisper-1',
            'language' => 'no',
            'response_format' => 'text',
        ]);

        if ($response->failed()) {
            Log::error('TranscribeCoachingSessionJob: Whisper API feilet', [
                'session_id' => $this->session->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Whisper API feilet: ' . $response->body());
        }

        $this->session->update([
            'transcription' => $response->body(),
        ]);

        // Start AI-oppsummering automatisk
        SummarizeCoachingSessionJob::dispatch($this->session->fresh());
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('TranscribeCoachingSessionJob feilet', [
            'session_id' => $this->session->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
