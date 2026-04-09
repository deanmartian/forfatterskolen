<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Engangs-rensekommando for å fjerne duplikate courses_taken-rader som
 * kan ha oppstått etter user-merge der samme bruker fikk to rader for
 * samme pakke. Disse duplikatene førte til at WebinarEmailOut sendte
 * den samme webinar-mailen to ganger til samme student.
 *
 * Strategi: for hver (user_id, package_id)-kombinasjon med >1 rad,
 * behold den NYESTE og slett resten. Audit alle slettinger til Log.
 *
 * Kjør med --dry-run for å se hva som ville blitt slettet uten å
 * faktisk slette noe.
 */
class CleanupDuplicateCoursesTaken extends Command
{
    protected $signature = 'coursestaken:dedupe {--dry-run : Vis hva som ville blitt slettet uten å gjøre det}';

    protected $description = 'Fjern duplikate courses_taken-rader (samme user+package) — behold nyeste';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun ? '=== DRY RUN — ingen rader slettes ===' : '=== KJØRER FOR ALVOR ===');

        // Finn alle (user_id, package_id)-grupper med mer enn én rad
        $duplicates = DB::table('courses_taken')
            ->select('user_id', 'package_id', DB::raw('COUNT(*) as count'), DB::raw('MAX(id) as keep_id'))
            ->whereNotNull('user_id')
            ->whereNotNull('package_id')
            ->groupBy('user_id', 'package_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('Ingen duplikater funnet. Alt er rent.');
            return self::SUCCESS;
        }

        $this->warn("Fant {$duplicates->count()} (user, package)-grupper med duplikater.");

        $totalToDelete = 0;
        $totalDeleted = 0;
        $deletedDetails = [];

        foreach ($duplicates as $dup) {
            // Hent alle ID-er for denne gruppen
            $allRows = DB::table('courses_taken')
                ->where('user_id', $dup->user_id)
                ->where('package_id', $dup->package_id)
                ->orderByDesc('id')
                ->get(['id', 'created_at', 'end_date']);

            // Behold den nyeste (høyeste id), slett resten
            $keepId = $allRows->first()->id;
            $deleteIds = $allRows->skip(1)->pluck('id')->all();

            $totalToDelete += count($deleteIds);

            $this->line("User {$dup->user_id} / Package {$dup->package_id}: behold #{$keepId}, slett " . count($deleteIds) . " duplikater (IDs: " . implode(',', $deleteIds) . ")");

            $deletedDetails[] = [
                'user_id' => $dup->user_id,
                'package_id' => $dup->package_id,
                'kept' => $keepId,
                'deleted' => $deleteIds,
            ];

            if (!$dryRun && !empty($deleteIds)) {
                $deleted = DB::table('courses_taken')->whereIn('id', $deleteIds)->delete();
                $totalDeleted += $deleted;
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("DRY RUN: ville slettet $totalToDelete rader. Kjør uten --dry-run for å gjøre det.");
        } else {
            $this->info("Slettet $totalDeleted rader fra courses_taken.");
            Log::info('CleanupDuplicateCoursesTaken: removed duplicates', [
                'total_deleted' => $totalDeleted,
                'groups' => count($deletedDetails),
                'details' => $deletedDetails,
            ]);
        }

        return self::SUCCESS;
    }
}
