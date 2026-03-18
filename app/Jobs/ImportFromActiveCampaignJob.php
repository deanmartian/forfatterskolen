<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Services\ActiveCampaignService;
use App\Services\ContactService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImportFromActiveCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // 1 time
    public int $tries = 1;

    public function __construct(
        public string $method = 'api',
        public ?string $filePath = null,
        public bool $importTags = true,
        public bool $matchUsers = true,
        public bool $skipDuplicates = true,
        public bool $importUnsubscribed = true,
    ) {}

    public function handle(ContactService $contactService): void
    {
        $acService = app(ActiveCampaignService::class);

        $stats = [
            'imported' => 0,
            'updated' => 0,
            'duplicates' => 0,
            'failed' => 0,
            'unsubscribed' => 0,
            'matched' => 0,
        ];

        $this->updateProgress('running', 0, $stats, 'Starter import...');

        $contacts = $this->method === 'api'
            ? $acService->getAllContacts()
            : $acService->parseCsv($this->filePath);

        $processed = 0;

        foreach ($contacts as $acContact) {
            $processed++;

            try {
                $email = strtolower(trim(
                    $acContact['email'] ?? $acContact['Email'] ?? ''
                ));

                if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $stats['failed']++;
                    $this->updateProgress('running', $processed, $stats);
                    continue;
                }

                // Sjekk status
                $status = $this->mapStatus($acContact['status'] ?? $acContact['Status'] ?? '1');

                if ($status === 'unsubscribed' && ! $this->importUnsubscribed) {
                    $stats['duplicates']++;
                    $this->updateProgress('running', $processed, $stats);
                    continue;
                }

                // Sjekk om allerede finnes
                $existing = Contact::where('email', $email)->first();

                if ($existing && $this->skipDuplicates) {
                    // Oppdater AC-ID hvis mangler
                    if (empty($existing->ac_id) && ! empty($acContact['id'])) {
                        $existing->update(['ac_id' => $acContact['id']]);
                    }
                    $stats['duplicates']++;
                    $this->updateProgress('running', $processed, $stats);
                    continue;
                }

                // Opprett eller oppdater kontakt
                $contact = $contactService->findOrCreateByEmail($email, [
                    'first_name' => $acContact['firstName'] ?? $acContact['first name'] ?? $acContact['first_name'] ?? null,
                    'last_name' => $acContact['lastName'] ?? $acContact['last name'] ?? $acContact['last_name'] ?? null,
                    'phone' => $acContact['phone'] ?? $acContact['Phone'] ?? null,
                    'source' => 'ac_import',
                    'ac_id' => $acContact['id'] ?? $acContact['ac_id'] ?? null,
                    'status' => $status,
                ]);

                if ($status === 'unsubscribed') {
                    $contact->update([
                        'status' => 'unsubscribed',
                        'unsubscribed_at' => $contact->unsubscribed_at ?? now(),
                    ]);
                    $stats['unsubscribed']++;
                }

                // Matchet mot bruker?
                if ($contact->user_id) {
                    $stats['matched']++;
                }

                if ($existing) {
                    $stats['updated']++;
                } else {
                    $stats['imported']++;
                }

                // Importer tags
                if ($this->importTags) {
                    $tags = $this->extractTags($acContact, $acService);

                    foreach ($tags as $tag) {
                        if (! empty($tag)) {
                            $contactService->tagContact($contact, strtolower(trim($tag)));
                        }
                    }
                }

                // Oppdater fremdrift hver 50. rad
                if ($processed % 50 === 0) {
                    $this->updateProgress('running', $processed, $stats);
                }

            } catch (\Exception $e) {
                $stats['failed']++;
                Log::error("AC-import feilet for rad {$processed}: {$e->getMessage()}");
            }
        }

        // Ferdig
        $this->updateProgress('completed', $processed, $stats, 'Import fullført!');
    }

    private function extractTags(array $acContact, ActiveCampaignService $acService): array
    {
        // Fra CSV: tags er komma-separert
        $csvTags = $acContact['tags'] ?? $acContact['Tags'] ?? '';
        if (! empty($csvTags)) {
            return array_map('trim', explode(',', $csvTags));
        }

        // Fra API: hent tags via ekstra kall
        if ($this->method === 'api' && ! empty($acContact['id'])) {
            return $acService->getContactTags($acContact['id']);
        }

        return [];
    }

    private function mapStatus(string $status): string
    {
        return match (strtolower($status)) {
            '0', 'unsubscribed' => 'unsubscribed',
            '2', 'bounced' => 'bounced',
            default => 'active',
        };
    }

    private function updateProgress(string $status, int $processed, array $stats, string $message = ''): void
    {
        Cache::put('ac_import_progress', [
            'status' => $status,
            'processed' => $processed,
            'stats' => $stats,
            'message' => $message,
            'updated_at' => now()->toIso8601String(),
        ], 7200); // 2 timer
    }
}
