<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE project_roadmap_steps 
            MODIFY COLUMN status ENUM('not_planned', 'not_started', 'started', 'finished') 
            DEFAULT 'not_planned'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE project_roadmap_steps 
            MODIFY COLUMN status ENUM('not_started', 'started', 'finished') 
            DEFAULT 'not_started'
        ");
    }
};
