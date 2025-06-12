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
        Schema::create('course_testimonials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->index('FK_course_testimonials_courses');
            $table->string('name');
            $table->text('testimony', 65535)->nullable();
            $table->string('user_image')->nullable();
            $table->boolean('is_video')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('course_testimonials');
    }
};
