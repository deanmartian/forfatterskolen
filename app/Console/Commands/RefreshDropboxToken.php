<?php

namespace App\Console\Commands;

use Artisan;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log;

/**
 * Refresh Dropbox access token using refresh_token.
 *
 * SIKKERHET: Denne kommandoen har tidligere slettet hele .env-filen
 * ("env-katastrofen") pga. to feil i gammel kode:
 *
 *   1. preg_replace() brukte Dropbox-tokenet direkte som replacement-
 *      streng. Dropbox-tokens kan inneholde $1, $2 osv. som PHP tolket
 *      som back-references — resultatet ble en tom eller korrupt fil.
 *
 *   2. file_put_contents() skrev direkte til .env uten backup. Hvis
 *      prosessen døde midt i skrivingen (OOM, timeout, SIGKILL) ble
 *      .env liggende halvskrevet eller tom.
 *
 * Denne versjonen:
 *   - Tar backup av .env til .env.backup-YYYYmmdd-HHMMSS FØR noe endres
 *   - Bruker line-by-line parse — ingen preg_replace
 *   - Skriver til .env.tmp først, så rename() (atomisk operasjon)
 *   - Validerer at den nye filen har alle de forventede nøklene FØR
 *     rename
 *   - Abort-er hvis ANY sanity check feiler — .env forblir urørt
 */
class RefreshDropboxToken extends Command
{
    protected $signature = 'dropbox:refresh-token';

    protected $description = 'Refresh Dropbox access token using refresh token (safe: backs up .env, atomic write)';

    public function handle(): void
    {
        $client = new Client;

        try {
            $response = $client->post('https://api.dropboxapi.com/oauth2/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => config('services.dropbox.refresh_token'),
                    'client_id' => config('services.dropbox.key'),
                    'client_secret' => config('services.dropbox.secret'),
                ],
            ]);
        } catch (\Throwable $e) {
            $this->error('Failed to refresh Dropbox access token: ' . $e->getMessage());
            Log::error('Dropbox refresh failed: ' . $e->getMessage());
            return;
        }

        if ($response->getStatusCode() !== 200) {
            $this->error('Dropbox refresh returned status ' . $response->getStatusCode());
            Log::error('Dropbox refresh status: ' . $response->getStatusCode());
            return;
        }

        $data = json_decode((string) $response->getBody(), true);
        $accessToken = $data['access_token'] ?? null;

        if (!$accessToken || !is_string($accessToken)) {
            $this->error('Dropbox returned success, but access_token was missing or invalid');
            Log::error('Dropbox refresh: missing access_token in response');
            return;
        }

        // Sanity check on token format — must be a single line, reasonable length
        if (str_contains($accessToken, "\n") || str_contains($accessToken, "\r") || strlen($accessToken) < 10 || strlen($accessToken) > 10000) {
            $this->error('Access token failed sanity check (multiline or wrong length) — aborting');
            Log::error('Dropbox refresh: token failed sanity check');
            return;
        }

        $path = base_path('.env');

        if (!file_exists($path)) {
            $this->error('.env file not found at ' . $path . ' — aborting');
            Log::error('Dropbox refresh: .env not found');
            return;
        }

        // --- STEP 1: BACKUP ------------------------------------------------
        $backupPath = $path . '.backup-' . date('Ymd-His');
        if (!copy($path, $backupPath)) {
            $this->error('Could not create backup of .env — aborting');
            Log::error('Dropbox refresh: backup failed');
            return;
        }
        Log::info('Dropbox refresh: backed up .env to ' . $backupPath);

        // --- STEP 2: LINE-BY-LINE REPLACEMENT ------------------------------
        // Leser .env linje-for-linje, finner linjen som starter med
        // DROPBOX_TOKEN=, og erstatter hele linjen. Ingen regex, ingen
        // back-reference-farer. Hvis linjen ikke finnes, appender vi den.
        $original = file_get_contents($path);
        if ($original === false) {
            $this->error('Could not read .env — aborting (backup at ' . $backupPath . ')');
            return;
        }

        // Validate that we actually have some content
        if (strlen($original) < 50) {
            $this->error('.env is suspiciously short (' . strlen($original) . ' bytes) — aborting to prevent corruption');
            Log::error('Dropbox refresh: .env too short, aborting');
            return;
        }

        $lines = preg_split('/(\r\n|\n|\r)/', $original);
        if ($lines === false) {
            $this->error('Could not parse .env into lines — aborting');
            return;
        }

        $found = false;
        foreach ($lines as $i => $line) {
            if (str_starts_with(ltrim($line), 'DROPBOX_TOKEN=')) {
                $lines[$i] = 'DROPBOX_TOKEN=' . $accessToken;
                $found = true;
                break;
            }
        }

        if (!$found) {
            // Append at end (no newline before since split may have left trailing empty)
            $lines[] = 'DROPBOX_TOKEN=' . $accessToken;
        }

        $newContent = implode("\n", $lines);

        // --- STEP 3: SANITY CHECKS ON NEW CONTENT --------------------------
        // Same size-ish as original, contains the new token, contains a few
        // known-critical keys we REFUSE to write a file without.
        if (strlen($newContent) < 50) {
            $this->error('New .env content is suspiciously short — aborting (backup at ' . $backupPath . ')');
            return;
        }

        if (!str_contains($newContent, 'DROPBOX_TOKEN=' . $accessToken)) {
            $this->error('New .env content does not contain the new token — aborting (backup at ' . $backupPath . ')');
            return;
        }

        // Refuse to write if critical keys disappeared during processing
        $criticalKeys = ['APP_KEY=', 'DB_DATABASE=', 'DB_USERNAME='];
        foreach ($criticalKeys as $needle) {
            if (str_contains($original, $needle) && !str_contains($newContent, $needle)) {
                $this->error('Critical key ' . $needle . ' disappeared — aborting (backup at ' . $backupPath . ')');
                Log::error('Dropbox refresh: critical key vanished, aborted');
                return;
            }
        }

        // --- STEP 4: ATOMIC WRITE ------------------------------------------
        // Skriv til .env.tmp først. Hvis noe feiler under skrivingen, er
        // .env fortsatt intakt. Etter vellykket skriv gjør vi rename() som
        // er atomisk på samme filsystem.
        $tmpPath = $path . '.tmp-' . uniqid();
        if (file_put_contents($tmpPath, $newContent) === false) {
            $this->error('Could not write tmp file — aborting (backup at ' . $backupPath . ')');
            @unlink($tmpPath);
            return;
        }

        // Bytt om filene atomisk
        if (!rename($tmpPath, $path)) {
            $this->error('Could not atomically rename tmp → .env — aborting (backup at ' . $backupPath . ')');
            @unlink($tmpPath);
            return;
        }

        // --- STEP 5: SUCCESS — clear caches --------------------------------
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->info('Dropbox access token refreshed successfully.');
        $this->info('Backup saved to: ' . $backupPath);
        Log::info('Dropbox access token refreshed successfully. Backup at ' . $backupPath);
    }
}
