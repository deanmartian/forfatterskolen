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
        Schema::table('similar_courses', function (Blueprint $table) {
            $table->foreign('course_id', 'similar_courses_ibfk_1')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('similar_course_id', 'similar_courses_ibfk_2')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('similar_courses', function (Blueprint $table) {
            $table->dropForeign('similar_courses_ibfk_1');
            $table->dropForeign('similar_courses_ibfk_2');
        });
    }
};
