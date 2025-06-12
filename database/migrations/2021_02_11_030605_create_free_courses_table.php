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
        Schema::create('free_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->text('description');
            $table->string('course_image')->default('');
            $table->string('url')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('free_courses');
    }
};
