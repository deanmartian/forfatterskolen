<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('survey_answer', function (Blueprint $table) {
            $table->foreign('survey_id', 'survey_answer_survey_id')->references('id')->on('survey')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('survey_question_id', 'survey_answer_survey_question_id')->references('id')->on('survey_question')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'survey_answer_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_answer', function (Blueprint $table) {
            $table->dropForeign('survey_answer_survey_id');
            $table->dropForeign('survey_answer_survey_question_id');
            $table->dropForeign('survey_answer_user_id');
        });
    }
};
