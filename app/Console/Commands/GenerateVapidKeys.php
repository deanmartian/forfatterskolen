<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid';
    protected $description = 'Generer VAPID-nøkler for Web Push-varsler';

    public function handle()
    {
        $key = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);

        if (!$key) {
            $this->error('Kunne ikke generere nøkkelpar. Sjekk at OpenSSL støtter prime256v1.');
            return 1;
        }

        $details = openssl_pkey_get_details($key);

        // Extract raw private key (32 bytes)
        $privateKeyRaw = str_pad($details['ec']['d'], 32, "\0", STR_PAD_LEFT);

        // Extract raw public key (uncompressed, 65 bytes: 0x04 || x || y)
        $x = str_pad($details['ec']['x'], 32, "\0", STR_PAD_LEFT);
        $y = str_pad($details['ec']['y'], 32, "\0", STR_PAD_LEFT);
        $publicKeyRaw = "\x04" . $x . $y;

        $publicKey = $this->base64UrlEncode($publicKeyRaw);
        $privateKey = $this->base64UrlEncode($privateKeyRaw);

        $this->info('VAPID-nøkler generert!');
        $this->newLine();
        $this->line('Legg dette til i .env-filen:');
        $this->newLine();
        $this->line("VAPID_PUBLIC_KEY={$publicKey}");
        $this->line("VAPID_PRIVATE_KEY={$privateKey}");
        $this->line("VAPID_SUBJECT=mailto:post@forfatterskolen.no");

        return 0;
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
