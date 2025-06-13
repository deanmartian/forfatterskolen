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
        Schema::table('calendar_note', function (Blueprint $table) {
            $table->foreign('course_id', 'FK_calendar_note_courses')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_note', function (Blueprint $table) {
            $table->dropForeign('FK_calendar_note_courses');
        });
    }
};
