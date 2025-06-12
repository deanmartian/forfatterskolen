<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE project_roadmap_steps MODIFY COLUMN status ENUM('not_started', 'started', 'finished') DEFAULT 'not_started'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE project_roadmap_steps MODIFY COLUMN status VARCHAR(255) DEFAULT 'not_started'");
    }
};
