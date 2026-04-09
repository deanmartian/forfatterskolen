<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Log;

/**
 * Trygg én-kommando for å sette opp en Google Ads-konvertering
 * i .env basert på snippet fra Google Ads-konsollen.
 *
 * Bruk:
 *   php artisan google-ads:set-conversion
 *
 * Kommandoen:
 *   1. Prompter etter type (lead/purchase/checkout)
 *   2. Prompter etter hele send_to-strengen fra Google Ads-snippet
 *      (f.eks. "AW-18021112843/QrAGCOfKjZkcEIu4kZFD")
 *   3. Validerer formatet (AW-siffer/label)
 *   4. Splitter i tracking-ID + label
 *   5. Oppdaterer BÅDE:
 *      - GOOGLE_ADS_ID (til "AW-<tracking-id>")
 *      - GOOGLE_ADS_CONVERSION_<TYPE> (til full send_to-strengen)
 *   6. Samme trygge mønster som env:update (backup, atomisk rename,
 *      sanity checks, kritiske-nøkler-refuse)
 *
 * Hele operasjonen er atomisk — enten begge verdier oppdateres
 * sammen, eller ingenting endres og .env er urørt.
 *
 * Lagd etter Sven påpekte at vanlig env:update var for usikker
 * fordi det krevde to separate kommandoer som kunne komme ut av
 * synk hvis han glemte en.
 */
class SetGoogleAdsConversion extends Command
{
    protected $signature = 'google-ads:set-conversion';

    protected $description = 'Trygg oppdatering av Google Ads conversion i .env (tracking-ID + label i ett)';

    public function handle(): int
    {
        $this->info('');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  Google Ads Conversion Setup');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('');

        // --- STEG 1: Type ---
        $type = $this->choice(
            'Hvilken type konvertering?',
            ['lead', 'purchase', 'checkout'],
            0
        );

        $envKey = 'GOOGLE_ADS_CONVERSION_' . strtoupper($type);
        $this->info('Oppdaterer: ' . $envKey);
        $this->info('');

        // --- STEG 2: Paste snippet ---
        $this->line('Lim inn hele send_to-strengen fra Google Ads-snippet:');
        $this->line('(format: AW-<tracking-id>/<label>, f.eks. AW-18021112843/QrAGCOfKjZkcEIu4kZFD)');
        $this->info('');

        $input = trim($this->ask('send_to-verdi'));

        if (empty($input)) {
            $this->error('Tom verdi — avbryter.');
            return 1;
        }

        // --- STEG 3: Valider format ---
        // Tillater AW-<digits>/<alphanumeric chars inkl - og _>
        if (!preg_match('/^AW-(\d+)\/([A-Za-z0-9_-]+)$/', $input, $matches)) {
            $this->error('Ugyldig format. Forventet: AW-<tracking-id>/<label>');
            $this->line('Eksempel: AW-18021112843/QrAGCOfKjZkcEIu4kZFD');
            return 1;
        }

        $trackingId = $matches[1];
        $label = $matches[2];
        $newGoogleAdsId = 'AW-' . $trackingId;
        $fullSendTo = $input;

        $this->info('');
        $this->info('Parset:');
        $this->info('  Tracking ID:  ' . $newGoogleAdsId);
        $this->info('  Label:        ' . $this->mask($label));
        $this->info('');

        // --- STEG 4: Les .env og vis nåværende verdier ---
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('.env ikke funnet på ' . $envPath);
            return 1;
        }

        $original = file_get_contents($envPath);
        if ($original === false || strlen($original) < 50) {
            $this->error('.env er uleselig eller mistenkelig kort.');
            return 1;
        }

        $lines = preg_split('/(\r\n|\n|\r)/', $original);
        if ($lines === false) {
            $this->error('Kunne ikke parse .env.');
            return 1;
        }

        $currentGoogleAdsId = $this->findEnvValue($lines, 'GOOGLE_ADS_ID');
        $currentConversion = $this->findEnvValue($lines, $envKey);

        $this->info('Nåværende verdier i .env:');
        $this->line('  GOOGLE_ADS_ID       = ' . ($currentGoogleAdsId ?? '(ikke satt)'));
        $this->line('  ' . $envKey . ' = ' . ($currentConversion !== null ? $this->mask($currentConversion) : '(ikke satt)'));
        $this->info('');

        $this->info('Nye verdier som vil settes:');
        $this->line('  GOOGLE_ADS_ID       = ' . $newGoogleAdsId);
        $this->line('  ' . $envKey . ' = ' . $this->mask($fullSendTo));
        $this->info('');

        // Advar hvis GOOGLE_ADS_ID endres
        if ($currentGoogleAdsId && $currentGoogleAdsId !== $newGoogleAdsId) {
            $this->warn('OBS: GOOGLE_ADS_ID endres fra ' . $currentGoogleAdsId . ' til ' . $newGoogleAdsId);
            $this->warn('Dette påvirker gtag-config på alle sider og kan påvirke remarketing-historikk.');
            $this->info('');
        }

        if (!$this->confirm('Skal jeg oppdatere .env med disse verdiene?', false)) {
            $this->info('Avbrutt. Ingen endringer gjort.');
            return 0;
        }

        // --- STEG 5: BACKUP ---
        $backupPath = $envPath . '.backup-' . date('Ymd-His');
        if (!copy($envPath, $backupPath)) {
            $this->error('Backup feilet — avbryter.');
            return 1;
        }
        $this->info('✓ Backup lagret: ' . $backupPath);

        // --- STEG 6: Oppdater begge nøkler atomisk ---
        $updates = [
            'GOOGLE_ADS_ID' => $newGoogleAdsId,
            $envKey => $fullSendTo,
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

        // --- STEG 7: Sanity checks ---
        if (strlen($newContent) < 50) {
            $this->error('Ny .env-content er mistenkelig kort — avbryter (backup: ' . $backupPath . ').');
            return 1;
        }

        foreach ($updates as $key => $value) {
            if (!str_contains($newContent, $key . '=' . $value)) {
                $this->error('Ny verdi for ' . $key . ' mangler i ny content — avbryter.');
                return 1;
            }
        }

        // Refuser hvis kritiske nøkler har forsvunnet
        $criticalKeys = ['APP_KEY=', 'DB_DATABASE=', 'DB_USERNAME='];
        foreach ($criticalKeys as $needle) {
            if (str_contains($original, $needle) && !str_contains($newContent, $needle)) {
                $this->error('Kritisk nøkkel ' . $needle . ' forsvant — avbryter (backup: ' . $backupPath . ').');
                Log::error('google-ads:set-conversion: kritisk nøkkel forsvant.');
                return 1;
            }
        }

        // --- STEG 8: Atomisk skriv ---
        $tmpPath = $envPath . '.tmp-' . uniqid();
        if (file_put_contents($tmpPath, $newContent) === false) {
            $this->error('Kunne ikke skrive tmp-fil — avbryter.');
            @unlink($tmpPath);
            return 1;
        }

        if (!rename($tmpPath, $envPath)) {
            $this->error('Atomisk rename feilet — avbryter.');
            @unlink($tmpPath);
            return 1;
        }

        // --- STEG 9: Clear caches ---
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->info('');
        $this->info('✓ GOOGLE_ADS_ID og ' . $envKey . ' oppdatert i .env');
        $this->info('✓ Config cache + app cache tømt');
        $this->info('✓ Backup: ' . $backupPath);
        $this->info('');
        $this->info('Neste steg:');
        $this->info('  - Hvis du skal sette opp en annen conversion-type (purchase/checkout):');
        $this->info('    php artisan google-ads:set-conversion');
        $this->info('  - Test at tracking fyrer: åpne forfatterskolen.no med Google Tag Assistant');
        $this->info('');

        Log::info('google-ads:set-conversion: ' . $envKey . ' = ' . $this->mask($fullSendTo));

        return 0;
    }

    /**
     * Finn verdien av en env-nøkkel i en liste av linjer.
     */
    private function findEnvValue(array $lines, string $key): ?string
    {
        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), $key . '=')) {
                return substr($line, strpos($line, '=') + 1);
            }
        }
        return null;
    }

    /**
     * Maskér en verdi for visning i terminalen.
     */
    private function mask(string $value): string
    {
        $len = strlen($value);
        if ($len === 0) return '(tom)';
        if ($len <= 8) return str_repeat('*', $len);
        return substr($value, 0, 4) . '...' . substr($value, -4) . ' (' . $len . ' tegn)';
    }
}
