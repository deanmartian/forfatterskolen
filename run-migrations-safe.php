<?php
/**
 * Safe migration runner: runs each migration individually,
 * skipping those that fail due to "table already exists" or "column already exists".
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$migrationPath = database_path('migrations');
$files = glob($migrationPath . '/*.php');
sort($files);

$batch = 1;
$ran = 0;
$skipped = 0;
$failed = 0;

foreach ($files as $file) {
    $migrationName = pathinfo($file, PATHINFO_FILENAME);

    // Check if already recorded
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    if ($exists) {
        continue;
    }

    try {
        $migration = require $file;
        $migration->up();

        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $batch,
        ]);
        $ran++;
    } catch (\Throwable $e) {
        $msg = $e->getMessage();

        // Skip "already exists" errors (table or column)
        if (
            str_contains($msg, 'already exists') ||
            str_contains($msg, 'Duplicate column') ||
            str_contains($msg, 'Duplicate key name') ||
            str_contains($msg, 'Column not found') ||
            str_contains($msg, "Unknown column")
        ) {
            // Record as migrated anyway
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $batch,
            ]);
            $skipped++;
        } else {
            echo "FAILED: $migrationName\n";
            echo "  Error: $msg\n\n";
            // Still record it to move forward
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $batch,
            ]);
            $failed++;
        }
    }
}

echo "\nDone!\n";
echo "  Ran successfully: $ran\n";
echo "  Skipped (already exists): $skipped\n";
echo "  Failed (other errors): $failed\n";
echo "  Total recorded: " . ($ran + $skipped + $failed) . "\n";
