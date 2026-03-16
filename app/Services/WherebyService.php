<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WherebyService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.whereby.dev/v1';

    public function __construct()
    {
        $this->apiKey = config('services.whereby.key');
    }

    /**
     * Opprett et Whereby-møterom
     *
     * @param string $endDate ISO 8601 dato for når rommet utløper
     * @return array ['roomUrl', 'hostRoomUrl', 'meetingId']
     */
    public function createMeeting(string $endDate): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/meetings', [
            'endDate' => $endDate,
            'fields' => ['hostRoomUrl'],
        ]);

        if ($response->failed()) {
            Log::error('Whereby: Kunne ikke opprette møte', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Kunne ikke opprette Whereby-møte: ' . $response->body());
        }

        $data = $response->json();

        return [
            'roomUrl' => $data['roomUrl'] ?? null,
            'hostRoomUrl' => $data['hostRoomUrl'] ?? null,
            'meetingId' => $data['meetingId'] ?? null,
        ];
    }

    /**
     * Slett et Whereby-møterom
     *
     * @param string $meetingId
     * @return bool
     */
    public function deleteMeeting(string $meetingId): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->delete($this->baseUrl . '/meetings/' . $meetingId);

        if ($response->failed()) {
            Log::error('Whereby: Kunne ikke slette møte', [
                'meetingId' => $meetingId,
                'status' => $response->status(),
            ]);
            return false;
        }

        return true;
    }
}
