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
        Schema::table('course_email_out_log', function (Blueprint $table) {
            $table->foreign('course_id', 'course_email_out_log_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_email_out_log', function (Blueprint $table) {
            $table->dropForeign('course_email_out_log_course_id');
        });
    }
};
