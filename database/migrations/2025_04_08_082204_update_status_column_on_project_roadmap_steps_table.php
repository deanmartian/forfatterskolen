<?php

use Illuminate\Database\Migrations\Migration;

class UpdateStatusColumnOnProjectRoadmapStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE project_roadmap_steps MODIFY COLUMN status ENUM('not_started', 'started', 'finished') DEFAULT 'not_started'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE project_roadmap_steps MODIFY COLUMN status VARCHAR(255) DEFAULT 'not_started'");
    }
}
