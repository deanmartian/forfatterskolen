<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey', function (Blueprint $table) {
            $table->foreign('course_id', 'survey_course_id_relation')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey', function (Blueprint $table) {
            $table->dropForeign('survey_course_id_relation');
        });
    }
}
