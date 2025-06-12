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
        Schema::table('course_discounts', function (Blueprint $table) {
            $table->foreign('course_id', 'FK_course_discounts_courses')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_discounts', function (Blueprint $table) {
            $table->dropForeign('FK_course_discounts_courses');
        });
    }
};
