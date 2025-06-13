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
        Schema::table('course_testimonials', function (Blueprint $table) {
            $table->foreign('course_id', 'FK_course_testimonials_courses')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_testimonials', function (Blueprint $table) {
            $table->dropForeign('FK_course_testimonials_courses');
        });
    }
};
