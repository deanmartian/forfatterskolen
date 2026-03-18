<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class ActiveCampaignService
{
    protected string $url;
    protected string $key;

    public function __construct()
    {
        $this->url = rtrim(config('services.activecampaign.url'), '/');
        $this->key = config('services.activecampaign.key');
    }

    /**
     * Test API-tilkobling og hent antall kontakter.
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Api-Token' => $this->key,
            ])->get($this->url . '/api/3/contacts', [
                'limit' => 1,
                'offset' => 0,
            ]);

            if ($response->successful()) {
                $total = $response->json('meta.total') ?? 0;
                return ['connected' => true, 'total' => (int) $total];
            }

            return ['connected' => false, 'error' => 'HTTP ' . $response->status()];
        } catch (\Exception $e) {
            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Hent alle kontakter fra ActiveCampaign via API (lazy).
     */
    public function getAllContacts(): LazyCollection
    {
        return LazyCollection::make(function () {
            $offset = 0;
            $limit = 100;

            do {
                $response = Http::withHeaders([
                    'Api-Token' => $this->key,
                ])->timeout(30)->get($this->url . '/api/3/contacts', [
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                if (! $response->successful()) {
                    Log::error('AC API feil', ['status' => $response->status(), 'offset' => $offset]);
                    break;
                }

                $contacts = $response->json('contacts', []);

                foreach ($contacts as $contact) {
                    yield $contact;
                }

                $offset += $limit;

                // Rate limiting — AC tillater ca. 5 req/sek
                usleep(250000); // 250ms

            } while (count($contacts) === $limit);
        });
    }

    /**
     * Hent tags for en kontakt.
     */
    public function getContactTags(string $contactId): array
    {
        try {
            $response = Http::withHeaders([
                'Api-Token' => $this->key,
            ])->timeout(15)->get($this->url . '/api/3/contacts/' . $contactId . '/contactTags');

            if (! $response->successful()) {
                return [];
            }

            $contactTags = $response->json('contactTags', []);
            $tags = [];

            foreach ($contactTags as $ct) {
                // Hent tag-navn via tag-ID
                $tagId = $ct['tag'] ?? null;
                if ($tagId) {
                    $tagResponse = Http::withHeaders([
                        'Api-Token' => $this->key,
                    ])->timeout(10)->get($this->url . '/api/3/tags/' . $tagId);

                    if ($tagResponse->successful()) {
                        $tags[] = $tagResponse->json('tag.tag', '');
                    }
                }
            }

            return array_filter($tags);
        } catch (\Exception $e) {
            Log::warning('Kunne ikke hente tags for AC-kontakt ' . $contactId, ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Parse CSV-fil fra ActiveCampaign.
     */
    public function parseCsv(string $filePath): LazyCollection
    {
        return LazyCollection::make(function () use ($filePath) {
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file);

            if (! $headers) {
                fclose($file);
                return;
            }

            $headers = array_map(fn ($h) => strtolower(trim($h)), $headers);

            while (($row = fgetcsv($file)) !== false) {
                if (count($row) === count($headers)) {
                    yield array_combine($headers, $row);
                }
            }

            fclose($file);
        });
    }
}
