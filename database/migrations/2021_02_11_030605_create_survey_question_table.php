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
        Schema::create('survey_question', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('survey_id')->index('survey_id');
            $table->string('title');
            $table->string('question_type');
            $table->string('option_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('survey_question');
    }
};
