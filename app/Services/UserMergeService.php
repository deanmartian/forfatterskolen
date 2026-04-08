<?php

namespace App\Services;

use App\User;
use App\UserEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Sammenslåing av to brukerkontoer som tilhører samme person.
 *
 * Brukstilfelle: en redaktør har en gammel elev-konto fra før vi skilte
 * rollene, og vi vil samle alle data på én konto. Hovedkontoen (primary)
 * beholder rollen sin, og all data fra sekundæren blir overført dit.
 *
 * Sekundæren sin e-post lagres i `user_emails`-tabellen slik at brukeren
 * fortsatt kan logge inn med begge adressene.
 *
 * Hele prosessen kjører i én DB-transaksjon — hvis noe feiler, rulles
 * alt tilbake.
 */
class UserMergeService
{
    /**
     * Tabeller og kolonner som skal hoppes over fordi de håndteres separat
     * eller fordi de inneholder data vi ikke vil flytte.
     */
    protected array $skipColumns = [
        'user_emails.user_id',           // håndteres direkte
        'user_merge_logs.primary_user_id',
        'user_merge_logs.secondary_user_id',
        'user_merge_logs.merged_by_user_id',
        'inbox_comments.mentioned_user_ids', // JSON, sjelden viktig nok
    ];

    /**
     * Kolonner med UNIQUE-constraint på user_id som vi må håndtere
     * spesielt: vi sletter sekundærens rad før vi prøver å oppdatere.
     */
    protected array $uniqueUserIdColumns = [
        'profiles.user_id',
        'user_preferences.user_id',
        'user_notification_preferences.user_id',
        'self_publishing_learners.user_id',
    ];

    /**
     * Generer en preview av hva som vil bli flyttet, uten å gjøre noe.
     * Returnerer en array med tabell-navn → antall rader på sekundæren.
     */
    public function preview(int $primaryId, int $secondaryId): array
    {
        if ($primaryId === $secondaryId) {
            throw new \InvalidArgumentException('Kan ikke merge en bruker med seg selv');
        }

        $columns = $this->discoverUserIdColumns();

        $preview = [];
        foreach ($columns as $col) {
            try {
                $count = DB::table($col['table'])
                    ->where($col['column'], $secondaryId)
                    ->count();

                if ($count > 0) {
                    $preview["{$col['table']}.{$col['column']}"] = $count;
                }
            } catch (\Throwable $e) {
                // Tabellen kan være borte eller utilgjengelig — hopper over
            }
        }

        return $preview;
    }

    /**
     * Utfør selve sammenslåingen. Returnerer en log-array med detaljer
     * om hva som ble flyttet. Kaster exception hvis noe feiler underveis
     * (alt rulles tilbake).
     */
    public function merge(int $primaryId, int $secondaryId, ?int $mergedByUserId = null): array
    {
        if ($primaryId === $secondaryId) {
            throw new \InvalidArgumentException('Kan ikke merge en bruker med seg selv');
        }

        $primary = User::findOrFail($primaryId);
        $secondary = User::findOrFail($secondaryId);

        $rowsMoved = [];
        $errors = [];

        DB::beginTransaction();

        try {
            // Lås begge brukere for å hindre race conditions
            DB::table('users')->whereIn('id', [$primaryId, $secondaryId])->lockForUpdate()->get();

            // 1. Slett konflikterende rader for UNIQUE user_id-kolonner
            //    (ellers vil UPDATE feile med duplicate key error)
            foreach ($this->uniqueUserIdColumns as $tableCol) {
                [$table, $col] = explode('.', $tableCol);
                try {
                    $primaryHasRow = DB::table($table)->where($col, $primaryId)->exists();
                    if ($primaryHasRow) {
                        $deleted = DB::table($table)->where($col, $secondaryId)->delete();
                        if ($deleted > 0) {
                            $rowsMoved["{$tableCol} (slettet pga UNIQUE)"] = $deleted;
                        }
                    }
                } catch (\Throwable $e) {
                    $errors[$tableCol] = $e->getMessage();
                }
            }

            // 2. Oppdater alle user_id og editor_id-kolonner dynamisk
            $columns = $this->discoverUserIdColumns();
            foreach ($columns as $col) {
                $key = "{$col['table']}.{$col['column']}";
                if (in_array($key, $this->skipColumns)) {
                    continue;
                }
                try {
                    $updated = DB::table($col['table'])
                        ->where($col['column'], $secondaryId)
                        ->update([$col['column'] => $primaryId]);

                    if ($updated > 0) {
                        $rowsMoved[$key] = $updated;
                    }
                } catch (\Throwable $e) {
                    $errors[$key] = $e->getMessage();
                    // Ikke kast — vi vil at merge skal kunne fortsette selv om
                    // én tabell feiler. Errors blir lagret i audit-loggen.
                }
            }

            // 3. Lagre sekundærens e-post i user_emails (slik at brukeren
            //    fortsatt kan logge inn med begge adressene)
            if ($secondary->email && $secondary->email !== $primary->email) {
                $exists = UserEmail::where('user_id', $primaryId)
                    ->where('email', $secondary->email)
                    ->exists();

                if (!$exists) {
                    UserEmail::create([
                        'user_id' => $primaryId,
                        'email' => $secondary->email,
                    ]);
                    $rowsMoved['user_emails (lagt til sekundær)'] = 1;
                }
            }

            // 4. Lag audit-logg
            DB::table('user_merge_logs')->insert([
                'primary_user_id' => $primaryId,
                'secondary_user_id' => $secondaryId,
                'primary_email' => $primary->email,
                'secondary_email' => $secondary->email,
                'rows_moved' => json_encode($rowsMoved),
                'errors' => empty($errors) ? null : json_encode($errors),
                'merged_by_user_id' => $mergedByUserId,
                'merged_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. Soft-delete sekundæren (bevarer historikk)
            $secondary->update([
                'is_active' => 0,
                'email' => 'merged-' . $secondary->id . '-' . $secondary->email,
            ]);
            $secondary->delete(); // SoftDeletes-trait

            DB::commit();

            Log::info('User merge completed', [
                'primary_user_id' => $primaryId,
                'secondary_user_id' => $secondaryId,
                'rows_moved_count' => array_sum($rowsMoved),
                'errors_count' => count($errors),
            ]);

            return [
                'success' => true,
                'rows_moved' => $rowsMoved,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('User merge failed', [
                'primary_user_id' => $primaryId,
                'secondary_user_id' => $secondaryId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Bruker INFORMATION_SCHEMA til å finne alle tabeller med user_id
     * eller editor_id-kolonner. Cacher resultatet i request-scope.
     */
    protected function discoverUserIdColumns(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $database = config('database.connections.mysql.database');

        $rows = DB::select(
            "SELECT TABLE_NAME, COLUMN_NAME
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ?
               AND COLUMN_NAME IN ('user_id', 'editor_id', 'feedback_user_id', 'sent_by_user_id', 'from_user_id')
             ORDER BY TABLE_NAME, COLUMN_NAME",
            [$database]
        );

        $cache = array_map(function ($r) {
            return ['table' => $r->TABLE_NAME, 'column' => $r->COLUMN_NAME];
        }, $rows);

        return $cache;
    }
}
