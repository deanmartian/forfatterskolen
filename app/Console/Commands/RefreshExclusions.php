<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Console\Command;

class RefreshExclusions extends Command
{
    protected $signature = 'crm:refresh-exclusions';

    protected $description = 'Oppdater e-post ekskluderinger basert på aktive kurs';

    public function handle(ContactService $contactService): int
    {
        $contacts = Contact::whereNotNull('user_id')->cursor();

        $processed = 0;

        $this->info('Oppdaterer ekskluderinger...');

        foreach ($contacts as $contact) {
            try {
                $contactService->refreshExclusions($contact);
                $processed++;

                if ($processed % 500 === 0) {
                    $this->info("Behandlet {$processed} kontakter...");
                }
            } catch (\Exception $e) {
                $this->warn("Feilet for kontakt {$contact->id}: {$e->getMessage()}");
            }
        }

        $this->info("Ferdig! {$processed} kontakter oppdatert.");

        return 0;
    }
}
