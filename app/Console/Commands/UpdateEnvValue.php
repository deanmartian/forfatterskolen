<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Log;

/**
 * Trygg oppdatering av én enkelt verdi i .env.
 *
 * Bruk:
 *   php artisan env:update DROPBOX_APP_SECRET
 *   php artisan env:update MAIL_PASSWORD
 *
 * Kommandoen prompter etter den nye verdien (skjult input, så den
 * havner ikke i bash-historikken) og gjør følgende:
 *
 *   1. Viser gammel verdi maskert (3 første + ... + 3 siste tegn)
 *      så du kan sammenligne uten å lekke full verdi i terminalen
 *   2. Tar backup til .env.backup-YYYYmmdd-HHMMSS
 *   3. Leser .env linje-for-linje, erstatter hele linjen med ny
 *      KEY=value (hvis nøkkelen ikke finnes, appender den)
 *   4. Sanity-sjekker at kritiske nøkler (APP_KEY, DB_DATABASE,
 *      DB_USERNAME) fortsatt finnes i ny content
 *   5. Skriver til .env.tmp-<uniqid>, deretter atomisk rename() til .env
 *   6. Kjører config:clear + cache:clear
 *
 * Aborter ved enhver feil — .env forblir urørt, backup peker på
 * forrige gyldige tilstand.
 *
 * DESIGN: Samme mønster som den fiksede RefreshDropboxToken (commit
 * dfe295ca) som erstattet den katastrofale preg_replace-baserte
 * versjonen som slettet hele .env-filen ("env-katastrofen").
 */
class UpdateEnvValue extends Command
{
    protected $signature = 'env:update {key : Navnet på env-nøkkelen (f.eks. DROPBOX_APP_SECRET)}';

    protected $description = 'Trygg oppdatering av én enkelt .env-verdi (med backup + atomisk skriv)';

    public function handle(): int
    {
        $key = $this->argument('key');

        // Valider nøkkel-format: A-Z, 0-9, underscore, må starte med A-Z
        if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) {
            $this->error('Ugyldig nøkkel-format. Nøkler må være STORE_BOKSTAVER (f.eks. DROPBOX_APP_SECRET).');
            return 1;
        }

        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env ikke funnet på ' . $envPath);
            return 1;
        }

        $original = file_get_contents($envPath);
        if ($original === false) {
            $this->error('Kunne ikke lese .env');
            return 1;
        }

        if (strlen($original) < 50) {
            $this->error('.env er mistenkelig kort (' . strlen($original) . ' bytes) — avbryter for å unngå korrupsjon.');
            return 1;
        }

        // --- FINN EKSISTERENDE VERDI (maskert preview) ---
        $lines = preg_split('/(\r\n|\n|\r)/', $original);
        if ($lines === false) {
            $this->error('Kunne ikke parse .env i linjer');
            return 1;
        }

        $currentValue = null;
        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), $key . '=')) {
                $eqPos = strpos($line, '=');
                $currentValue = substr($line, $eqPos + 1);
                break;
            }
        }

        if ($currentValue !== null) {
            $this->info('');
            $this->info('Nåværende verdi for ' . $key . ':');
            $this->line('  ' . $this->maskValue($currentValue));
            $this->info('');
        } else {
            $this->info('');
            $this->warn($key . ' finnes ikke i .env — vil bli lagt til som en ny linje.');
            $this->info('');
        }

        // --- SPØR ETTER NY VERDI (skjult input) ---
        $newValue = $this->secret('Skriv inn ny verdi (input er skjult)');

        if ($newValue === null || $newValue === '') {
            $this->error('Tom verdi er ikke tillatt — avbryter.');
            return 1;
        }

        // Sanity: ingen newlines tillatt
        if (str_contains($newValue, "\n") || str_contains($newValue, "\r")) {
            $this->error('Verdien kan ikke inneholde linjeskift — avbryter.');
            return 1;
        }

        // Sanity: rimelig lengde
        if (strlen($newValue) > 10000) {
            $this->error('Verdien er mistenkelig lang (' . strlen($newValue) . ' tegn) — avbryter.');
            return 1;
        }

        // --- BEKREFT ---
        $this->info('');
        $this->info('Ny verdi for ' . $key . ':');
        $this->line('  ' . $this->maskValue($newValue));
        $this->info('');

        if (!$this->confirm('Skal jeg oppdatere .env med denne verdien?', false)) {
            $this->info('Avbrutt. Ingen endringer gjort.');
            return 0;
        }

        // --- STEP 1: BACKUP ---
        $backupPath = $envPath . '.backup-' . date('Ymd-His');
        if (!copy($envPath, $backupPath)) {
            $this->error('Backup feilet — avbryter.');
            return 1;
        }
        $this->info('✓ Backup lagret: ' . $backupPath);

        // --- STEP 2: LINE-BY-LINE REPLACEMENT ---
        $found = false;
        foreach ($lines as $i => $line) {
            if (str_starts_with(ltrim($line), $key . '=')) {
                $lines[$i] = $key . '=' . $newValue;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $lines[] = $key . '=' . $newValue;
        }

        $newContent = implode("\n", $lines);

        // --- STEP 3: SANITY CHECKS ---
        if (strlen($newContent) < 50) {
            $this->error('Ny content er mistenkelig kort — avbryter (backup: ' . $backupPath . ').');
            return 1;
        }

        if (!str_contains($newContent, $key . '=' . $newValue)) {
            $this->error('Ny verdi finnes ikke i ny content — avbryter (backup: ' . $backupPath . ').');
            return 1;
        }

        // Verifiser at kritiske nøkler ikke har forsvunnet
        $criticalKeys = ['APP_KEY=', 'DB_DATABASE=', 'DB_USERNAME='];
        foreach ($criticalKeys as $needle) {
            if (str_contains($original, $needle) && !str_contains($newContent, $needle)) {
                $this->error('Kritisk nøkkel ' . $needle . ' forsvant — avbryter (backup: ' . $backupPath . ').');
                Log::error('env:update: kritisk nøkkel forsvant, avbrutt. Key=' . $key);
                return 1;
            }
        }

        // --- STEP 4: ATOMIC WRITE ---
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

        // --- STEP 5: CLEAR CACHES ---
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->info('');
        $this->info('✓ ' . $key . ' oppdatert i .env');
        $this->info('✓ Config cache + app cache tømt');
        $this->info('');
        $this->info('Backup tilgjengelig på: ' . $backupPath);
        $this->info('');

        Log::info('env:update: oppdaterte ' . $key . '. Backup: ' . $backupPath);

        return 0;
    }

    /**
     * Masker en verdi for visning i terminalen.
     * Viser 3 første + ... + 3 siste tegn hvis verdien er lang nok,
     * ellers bare *****.
     */
    private function maskValue(string $value): string
    {
        $len = strlen($value);
        if ($len === 0) {
            return '(tom)';
        }
        if ($len <= 6) {
            return str_repeat('*', $len) . ' (' . $len . ' tegn)';
        }
        return substr($value, 0, 3) . '...' . substr($value, -3) . ' (' . $len . ' tegn)';
    }
}
