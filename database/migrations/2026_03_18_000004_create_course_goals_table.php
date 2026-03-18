<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('courses_taken_id');
            $table->text('goal');
            $table->timestamps();

            $table->unique(['user_id', 'courses_taken_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('courses_taken_id')->references('id')->on('courses_taken')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_goals');
    }
};
