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
        Schema::create('similar_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->integer('similar_course_id')->unsigned()->index('similar_course_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('similar_courses');
    }
};
