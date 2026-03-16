<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$userMigrations = [
    '2021_02_17_075113_update_user_table.php',
    '2021_03_25_020811_add_column_admin_with_editor_access_on_user_table.php',
    '2021_04_28_065827_add_column_with_head_editor_accesson_table_users.php',
    '2021_07_13_090421_add_could_buy_course_field_in_users_table.php',
    '2022_05_24_084045_add_is_active_field_in_users_table.php',
    '2022_08_16_102456_add_default_password_field_to_users_table.php',
    '2022_09_22_072220_add_admin_with_giutbok_access_field_to_users_table.php',
    '2022_09_23_070057_add_is_self_publishing_learner_field_to_users_table.php',
    '2022_09_27_103106_add_multiple_admin_fields_to_users_table.php',
    '2023_01_30_085814_add_fiken_contact_id_to_users_table.php',
    '2024_11_20_093107_add_email_verification_fields_to_users_table.php',
    '2025_08_18_085234_add_disable_date_in_users_table.php',
];

$migrationPath = database_path('migrations');
foreach ($userMigrations as $file) {
    $path = $migrationPath . '/' . $file;
    if (!file_exists($path)) {
        echo "NOT FOUND: $file\n";
        continue;
    }
    try {
        $migration = require $path;
        $migration->up();
        echo "OK: $file\n";
    } catch (\Throwable $e) {
        $msg = $e->getMessage();
        if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate')) {
            echo "SKIP (exists): $file\n";
        } else {
            echo "FAIL: $file - $msg\n";
        }
    }
}

echo "\nFinal columns: " . implode(', ', Schema::getColumnListing('users')) . "\n";
