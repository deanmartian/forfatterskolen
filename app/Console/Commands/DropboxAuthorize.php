<?php

namespace App\Console\Commands;

use Artisan;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log;

/**
 * Kjør OAuth 2.0 authorization code-flow for Dropbox for å få ny
 * access_token + refresh_token. Brukes når refresh_token er ugyldig
 * eller revoked (f.eks. etter at app_secret er rotert, eller første
 * gangs oppsett).
 *
 * Flyt:
 *   1. Kommandoen printer en authorize-URL
 *   2. Sven åpner URL-en i nettleseren og logger inn med Dropbox-kontoen
 *      som eier Forfatterskolen-appen
 *   3. Dropbox viser en autorisasjonskode (eller redirecter til redirect
 *      URI med ?code=... i URL-en)
 *   4. Sven limer inn koden i terminalen
 *   5. Kommandoen bytter koden mot { access_token, refresh_token } via
 *      POST til /oauth2/token
 *   6. Begge verdiene skrives inn i .env med samme trygge mønster som
 *      env:update (backup + atomisk rename + sanity checks)
 *
 * Bruk:
 *   php artisan dropbox:authorize
 */
class DropboxAuthorize extends Command
{
    protected $signature = 'dropbox:authorize';

    protected $description = 'Kjør OAuth-authorize-flyt for å få nytt Dropbox refresh_token (brukes når det gamle er revoked)';

    public function handle(): int
    {
        $appKey = config('services.dropbox.key');
        $appSecret = config('services.dropbox.secret');

        if (empty($appKey) || empty($appSecret)) {
            $this->error('DROPBOX_APP_KEY og DROPBOX_APP_SECRET må være satt i .env før du kjører denne kommandoen.');
            $this->info('Bruk "php artisan env:update DROPBOX_APP_KEY" og "php artisan env:update DROPBOX_APP_SECRET" først.');
            return 1;
        }

        // Bygg authorize-URL med token_access_type=offline for å få
        // refresh_token tilbake (default er bare short-lived access_token)
        $authorizeUrl = 'https://www.dropbox.com/oauth2/authorize'
            . '?client_id=' . urlencode($appKey)
            . '&response_type=code'
            . '&token_access_type=offline';

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  Dropbox OAuth Authorization');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('');
        $this->info('1. Åpne denne URL-en i nettleseren (kopier hele linjen):');
        $this->info('');
        $this->line('   ' . $authorizeUrl);
        $this->info('');
        $this->info('2. Logg inn med Dropbox-kontoen som eier Forfatterskolen-appen.');
        $this->info('');
        $this->info('3. Klikk "Allow" / "Tillat".');
        $this->info('');
        $this->info('4. Dropbox viser en AUTHORIZATION CODE på siden. Kopier den.');
        $this->info('   (Hvis appen har redirect URI konfigurert i Dropbox Console,');
        $this->info('    finner du koden i ?code=... i URL-en etter redirect).');
        $this->info('');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('');

        $code = $this->ask('Lim inn authorization code her');

        if (empty($code)) {
            $this->error('Ingen kode gitt — avbryter.');
            return 1;
        }

        $code = trim($code);

        // Sanity: koden skal ikke inneholde newlines eller mellomrom
        if (str_contains($code, ' ') || str_contains($code, "\n") || str_contains($code, "\r")) {
            $this->error('Koden ser feil ut (inneholder mellomrom eller linjeskift). Kopier den igjen og prøv på nytt.');
            return 1;
        }

        $this->info('');
        $this->info('Bytter kode mot access_token + refresh_token...');
        $this->info('');

        // --- BYTT KODE MOT TOKENS ---
        try {
            $client = new Client;
            $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
                'form_params' => [
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'client_id' => $appKey,
                    'client_secret' => $appSecret,
                ],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            $this->error('Dropbox avviste kodeutvekslingen:');
            $this->line('  ' . $body);
            $this->info('');
            $this->info('Vanlige årsaker:');
            $this->info('  - Koden er allerede brukt (hver kode er engangs-bruk)');
            $this->info('  - Koden er utløpt (varer bare noen minutter)');
            $this->info('  - client_id/secret matcher ikke appen som utstedte koden');
            $this->info('');
            $this->info('Løsning: Kjør kommandoen på nytt og hent en frisk kode fra authorize-URL-en.');
            return 1;
        } catch (\Throwable $e) {
            $this->error('Uventet feil: ' . $e->getMessage());
            return 1;
        }

        if ($response->getStatusCode() !== 200) {
            $this->error('Dropbox returnerte status ' . $response->getStatusCode() . ' — avbryter.');
            return 1;
        }

        $data = json_decode((string) $response->getBody(), true);
        $accessToken = $data['access_token'] ?? null;
        $refreshToken = $data['refresh_token'] ?? null;
        $expiresIn = $data['expires_in'] ?? null;

        if (!$accessToken || !$refreshToken) {
            $this->error('Dropbox returnerte suksess men mangler access_token eller refresh_token — avbryter.');
            return 1;
        }

        // Sanity: ingen newlines
        foreach ([$accessToken, $refreshToken] as $tok) {
            if (str_contains($tok, "\n") || str_contains($tok, "\r")) {
                $this->error('Token inneholder linjeskift — avbryter (uventet format fra Dropbox).');
                return 1;
            }
        }

        $this->info('✓ Dropbox bekreftet autorisasjon og ga oss tokens:');
        $this->line('  access_token:  ' . $this->mask($accessToken));
        $this->line('  refresh_token: ' . $this->mask($refreshToken));
        if ($expiresIn) {
            $this->line('  expires_in:    ' . $expiresIn . ' sekunder (' . round($expiresIn / 3600, 1) . ' timer)');
        }
        $this->info('');

        if (!$this->confirm('Skriv begge verdiene til .env nå?', true)) {
            $this->info('Avbrutt. .env er urørt.');
            return 0;
        }

        // --- OPPDATER .env ATOMISK (begge nøklene i én operasjon) ---
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            $this->error('.env ikke funnet — avbryter.');
            return 1;
        }

        $original = file_get_contents($envPath);
        if ($original === false || strlen($original) < 50) {
            $this->error('.env er uleselig eller mistenkelig kort — avbryter.');
            return 1;
        }

        // Backup
        $backupPath = $envPath . '.backup-' . date('Ymd-His');
        if (!copy($envPath, $backupPath)) {
            $this->error('Backup feilet — avbryter.');
            return 1;
        }
        $this->info('✓ Backup: ' . $backupPath);

        // Line-by-line replacement for BEGGE nøklene
        $lines = preg_split('/(\r\n|\n|\r)/', $original);
        if ($lines === false) {
            $this->error('Kunne ikke parse .env — avbryter (backup: ' . $backupPath . ').');
            return 1;
        }

        $updates = [
            'DROPBOX_TOKEN' => $accessToken,
            'DROPBOX_REFRESH_TOKEN' => $refreshToken,
        ];

        foreach ($updates as $key => $value) {
            $found = false;
            foreach ($lines as $i => $line) {
                if (str_starts_with(ltrim($line), $key . '=')) {
                    $lines[$i] = $key . '=' . $value;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $lines[] = $key . '=' . $value;
            }
        }

        $newContent = implode("\n", $lines);

        // Sanity checks
        if (strlen($newContent) < 50) {
            $this->error('Ny content mistenkelig kort — avbryter (backup: ' . $backupPath . ').');
            return 1;
        }

        foreach ($updates as $key => $value) {
            if (!str_contains($newContent, $key . '=' . $value)) {
                $this->error('Ny verdi for ' . $key . ' finnes ikke i ny content — avbryter (backup: ' . $backupPath . ').');
                return 1;
            }
        }

        $criticalKeys = ['APP_KEY=', 'DB_DATABASE=', 'DB_USERNAME='];
        foreach ($criticalKeys as $needle) {
            if (str_contains($original, $needle) && !str_contains($newContent, $needle)) {
                $this->error('Kritisk nøkkel ' . $needle . ' forsvant — avbryter (backup: ' . $backupPath . ').');
                return 1;
            }
        }

        // Atomisk skriv
        $tmpPath = $envPath . '.tmp-' . uniqid();
        if (file_put_contents($tmpPath, $newContent) === false) {
            $this->error('Kunne ikke skrive tmp-fil — avbryter (backup: ' . $backupPath . ').');
            @unlink($tmpPath);
            return 1;
        }

        if (!rename($tmpPath, $envPath)) {
            $this->error('Atomisk rename feilet — avbryter (backup: ' . $backupPath . ').');
            @unlink($tmpPath);
            return 1;
        }

        // Clear caches
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->info('');
        $this->info('✓ DROPBOX_TOKEN og DROPBOX_REFRESH_TOKEN oppdatert i .env');
        $this->info('✓ Config cache + app cache tømt');
        $this->info('✓ Backup: ' . $backupPath);
        $this->info('');
        $this->info('Dropbox er nå klar til bruk. Test f.eks. ved å laste opp et manus i');
        $this->info('/self-publishing, eller kjør:');
        $this->info('  php artisan dropbox:refresh-token');
        $this->info('for å verifisere at det gamle refresh-flowen også fungerer.');
        $this->info('');

        Log::info('dropbox:authorize: fullført. Nye tokens lagret. Backup: ' . $backupPath);

        return 0;
    }

    /**
     * Masker en token for visning — 3 første + ... + 3 siste + lengde.
     */
    private function mask(string $value): string
    {
        $len = strlen($value);
        if ($len <= 6) {
            return str_repeat('*', $len) . ' (' . $len . ' tegn)';
        }
        return substr($value, 0, 3) . '...' . substr($value, -3) . ' (' . $len . ' tegn)';
    }
}
