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
        Schema::table('survey_question', function (Blueprint $table) {
            $table->foreign('survey_id', 'survey_id_ibfk_1')->references('id')->on('survey')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_question', function (Blueprint $table) {
            $table->dropForeign('survey_id_ibfk_1');
        });
    }
};
