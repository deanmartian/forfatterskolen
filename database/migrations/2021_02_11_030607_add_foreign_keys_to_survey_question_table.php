<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSurveyQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_question', function (Blueprint $table) {
            $table->foreign('survey_id', 'survey_id_ibfk_1')->references('id')->on('survey')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_question', function (Blueprint $table) {
            $table->dropForeign('survey_id_ibfk_1');
        });
    }
}
