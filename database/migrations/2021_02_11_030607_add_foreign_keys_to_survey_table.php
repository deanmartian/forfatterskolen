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
        Schema::table('survey', function (Blueprint $table) {
            $table->foreign('course_id', 'survey_course_id_relation')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey', function (Blueprint $table) {
            $table->dropForeign('survey_course_id_relation');
        });
    }
};
