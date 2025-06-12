<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `manuscript_editor_can_takes` MODIFY `note` VARCHAR(1000) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `manuscript_editor_can_takes` MODIFY `note` VARCHAR(1000) NOT NULL');
    }
};
