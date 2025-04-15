<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotPlannedToStatusEnumOnProjectRoadmapStepsTable extends Migration
{
    public function up()
    {
        DB::statement("
            ALTER TABLE project_roadmap_steps 
            MODIFY COLUMN status ENUM('not_planned', 'not_started', 'started', 'finished') 
            DEFAULT 'not_planned'
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE project_roadmap_steps 
            MODIFY COLUMN status ENUM('not_started', 'started', 'finished') 
            DEFAULT 'not_started'
        ");
    }
}
