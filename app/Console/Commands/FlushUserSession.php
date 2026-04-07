<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FlushUserSession extends Command
{
    protected $signature = 'session:flush-user {email : E-postadressen til brukeren}';
    protected $description = 'Slett alle sesjoner for en bestemt bruker (tvinger ny innlogging)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = \App\User::where('email', $email)->first();

        if (!$user) {
            $this->error("Bruker med e-post '{$email}' ikke funnet.");
            return self::FAILURE;
        }

        $this->info("Bruker funnet: #{$user->id} {$user->full_name} ({$user->email})");

        $sessionPath = storage_path('framework/sessions');
        if (!is_dir($sessionPath)) {
            $this->error("Sesjonsmappe finnes ikke: {$sessionPath}");
            return self::FAILURE;
        }

        $files = File::files($sessionPath);
        $deleted = 0;

        foreach ($files as $file) {
            try {
                $content = file_get_contents($file->getPathname());
                // Session files contain serialized data with the user ID
                if (str_contains($content, '"login_web_') && str_contains($content, (string) $user->id)) {
                    // Double check by unserializing
                    $data = @unserialize($content);
                    if ($data) {
                        // Check all login guards
                        foreach ($data as $key => $value) {
                            if (str_starts_with($key, 'login_web_') && $value == $user->id) {
                                unlink($file->getPathname());
                                $deleted++;
                                break;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Skip unreadable files
            }
        }

        if ($deleted > 0) {
            $this->info("Slettet {$deleted} sesjon(er) for {$user->full_name}. Brukeren må logge inn på nytt.");
        } else {
            $this->warn("Ingen aktive sesjoner funnet for {$user->full_name}.");
        }

        return self::SUCCESS;
    }
}
