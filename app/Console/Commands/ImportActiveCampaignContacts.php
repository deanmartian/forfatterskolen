<?php

namespace App\Console\Commands;

use App\Services\ContactService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportActiveCampaignContacts extends Command
{
    protected $signature = 'crm:import-ac {file : Sti til CSV-fil fra ActiveCampaign}';

    protected $description = 'Importer kontakter fra ActiveCampaign CSV-eksport';

    public function handle(ContactService $contactService): int
    {
        $file = $this->argument('file');

        if (! file_exists($file)) {
            $this->error("Filen finnes ikke: {$file}");
            return 1;
        }

        $handle = fopen($file, 'r');
        if (! $handle) {
            $this->error("Kunne ikke åpne filen: {$file}");
            return 1;
        }

        // Les header-rad
        $headers = fgetcsv($handle);
        if (! $headers) {
            $this->error('Filen er tom eller har ugyldig format.');
            fclose($handle);
            return 1;
        }

        // Normaliser header-navn
        $headers = array_map(fn ($h) => strtolower(trim($h)), $headers);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        $total = 0;

        $this->info('Starter import fra ActiveCampaign...');
        $this->newLine();

        while (($row = fgetcsv($handle)) !== false) {
            $total++;

            try {
                $data = array_combine($headers, $row);

                $email = strtolower(trim($data['email'] ?? ''));
                if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    continue;
                }

                // Sjekk om kontakten allerede finnes
                $existing = \App\Models\Contact::where('email', $email)->first();

                $contact = $contactService->findOrCreateByEmail($email, [
                    'first_name' => $data['first name'] ?? $data['first_name'] ?? null,
                    'last_name' => $data['last name'] ?? $data['last_name'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'source' => 'ac_import',
                    'ac_id' => $data['ac_id'] ?? $data['id'] ?? null,
                    'status' => $this->mapStatus($data['status'] ?? 'active'),
                ]);

                if ($existing) {
                    $updated++;
                } else {
                    $created++;
                }

                // Importer tags
                $tags = $data['tags'] ?? '';
                if (! empty($tags)) {
                    $tagList = array_map('trim', explode(',', $tags));
                    foreach ($tagList as $tag) {
                        if (! empty($tag)) {
                            $contactService->tagContact($contact, $tag);
                        }
                    }
                }

                // Vis fremdrift hver 500. rad
                if ($total % 500 === 0) {
                    $this->info("Behandlet {$total} rader...");
                }

            } catch (\Exception $e) {
                $failed++;
                Log::error("AC-import feilet for rad {$total}: {$e->getMessage()}");
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info('Import fullført!');
        $this->table(
            ['Totalt', 'Opprettet', 'Oppdatert', 'Hoppet over', 'Feilet'],
            [[$total, $created, $updated, $skipped, $failed]]
        );

        return 0;
    }

    private function mapStatus(string $status): string
    {
        return match (strtolower($status)) {
            'unsubscribed', '2' => 'unsubscribed',
            'bounced', '3' => 'bounced',
            default => 'active',
        };
    }
}
