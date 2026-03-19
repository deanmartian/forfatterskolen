<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BigMarkerService
{
    private string $apiKey;
    private string $baseUrl;
    private string $channelId;

    public function __construct()
    {
        $this->apiKey = config('services.big_marker.api_key');
        $this->channelId = config('services.big_marker.channel_id');
        $this->baseUrl = config('services.big_marker.base_url', 'https://www.bigmarker.com/api/v1');
    }

    /**
     * Generisk API-kall mot BigMarker
     */
    private function request(string $method, string $endpoint, array $data = [])
    {
        $response = Http::withHeaders([
            'API-KEY' => $this->apiKey,
        ])->{$method}("{$this->baseUrl}/{$endpoint}", $data);

        if (!$response->successful()) {
            Log::error("BigMarker API feil: {$endpoint}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception("BigMarker API feil: {$response->status()} - {$response->body()}");
        }

        return $response->json();
    }

    /**
     * Opprett webinar (conference) i BigMarker
     */
    public function createConference(array $data): array
    {
        return $this->request('post', 'conferences', [
            'channel_id' => $this->channelId,
            'title' => $data['title'],
            'start_time' => $data['starts_at']->format('Y-m-d H:i:s'),
            'end_time' => $data['starts_at']->copy()->addHours($data['duration_hours'] ?? 1)->format('Y-m-d H:i:s'),
            'time_zone' => 'Stockholm',
            'description' => $data['description'] ?? '',
            'privacy' => 'private',
            'enable_knock_to_enter' => false,
            'send_reminder_emails_to_presenters' => false,
        ]);
    }

    /**
     * Oppdater webinar
     */
    public function updateConference(string $conferenceId, array $data): array
    {
        return $this->request('put', "conferences/{$conferenceId}", $data);
    }

    /**
     * Slett webinar
     */
    public function deleteConference(string $conferenceId): array
    {
        return $this->request('delete', "conferences/{$conferenceId}");
    }

    /**
     * Hent detaljer om webinar
     */
    public function getConference(string $conferenceId): array
    {
        return $this->request('get', "conferences/{$conferenceId}");
    }

    /**
     * Registrer deltaker til webinar
     */
    public function registerAttendee(string $conferenceId, array $attendee): array
    {
        return $this->request('post', "conferences/{$conferenceId}/register", [
            'email' => $attendee['email'],
            'first_name' => $attendee['first_name'] ?? '',
            'last_name' => $attendee['last_name'] ?? '',
        ]);
    }

    /**
     * Registrer flere deltakere på én gang
     */
    public function registerBulkAttendees(string $conferenceId, array $attendees): array
    {
        $results = [];
        foreach ($attendees as $attendee) {
            try {
                $results[] = $this->registerAttendee($conferenceId, $attendee);
            } catch (\Exception $e) {
                Log::warning("BigMarker registrering feilet for {$attendee['email']}: {$e->getMessage()}");
                $results[] = ['error' => $e->getMessage(), 'email' => $attendee['email']];
            }
        }
        return $results;
    }

    /**
     * Hent alle registrerte deltakere
     */
    public function getRegistrants(string $conferenceId, int $page = 1): array
    {
        return $this->request('get', "conferences/{$conferenceId}/registrations", [
            'page' => $page,
        ]);
    }

    /**
     * Hent join-URL for en spesifikk deltaker (enter_uri)
     */
    public function getJoinUrl(string $conferenceId, string $email): ?string
    {
        try {
            $result = $this->request('get', "conferences/{$conferenceId}/registrations", [
                'email' => $email,
            ]);

            // BigMarker returnerer enter_uri per deltaker
            if (!empty($result['registrations'])) {
                foreach ($result['registrations'] as $reg) {
                    if (strtolower($reg['email'] ?? '') === strtolower($email)) {
                        return $reg['enter_uri'] ?? null;
                    }
                }
            }

            return $result['enter_uri'] ?? null;
        } catch (\Exception $e) {
            Log::warning("Kunne ikke hente join-URL for {$email}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Hent recording URL for et webinar
     */
    public function getRecordingUrl(string $conferenceId): ?string
    {
        try {
            $data = $this->getConference($conferenceId);
            $url = $data['recording_url'] ?? null;

            if ($url && $url !== 'not available') {
                return $url;
            }

            return null;
        } catch (\Exception $e) {
            Log::warning("Kunne ikke hente recording for {$conferenceId}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Deaktiver BigMarkers egne e-poster for et webinar
     */
    public function disableEmails(string $conferenceId): array
    {
        return $this->updateConference($conferenceId, [
            'enable_registration_email' => false,
            'send_reminder_emails_to_presenters' => false,
            'send_cancellation_email' => false,
            'enable_review_emails' => false,
        ]);
    }
}
