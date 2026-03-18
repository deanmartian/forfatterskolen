<?php

namespace App\Console\Commands;

use App\Services\ContactService;
use App\User;
use Illuminate\Console\Command;

class SyncUsersToContacts extends Command
{
    protected $signature = 'crm:sync-users';

    protected $description = 'Synkroniser alle brukere til contacts-tabellen';

    public function handle(ContactService $contactService): int
    {
        $users = User::whereNotNull('email')
            ->where('email', '!=', '')
            ->cursor();

        $synced = 0;

        $this->info('Starter synkronisering av brukere til kontakter...');

        foreach ($users as $user) {
            try {
                $contactService->syncUserToContact($user);
                $synced++;

                if ($synced % 500 === 0) {
                    $this->info("Synkronisert {$synced} brukere...");
                }
            } catch (\Exception $e) {
                $this->warn("Feilet for bruker {$user->id} ({$user->email}): {$e->getMessage()}");
            }
        }

        $this->info("Ferdig! {$synced} brukere synkronisert til kontakter.");

        return 0;
    }
}
