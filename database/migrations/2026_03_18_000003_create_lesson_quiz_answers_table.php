<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('lesson_quiz_id');
            $table->unsignedTinyInteger('selected_option');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'lesson_quiz_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lesson_quiz_id')->references('id')->on('lesson_quizzes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_quiz_answers');
    }
};
