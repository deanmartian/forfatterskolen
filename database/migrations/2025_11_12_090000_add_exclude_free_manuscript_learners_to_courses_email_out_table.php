<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExcludeFreeManuscriptLearnersToCoursesEmailOutTable extends Migration
{
    public function up()
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->boolean('exclude_free_manuscript_learners')->default(false);
        });
    }

    public function down()
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->dropColumn('exclude_free_manuscript_learners');
        });
    }
}
