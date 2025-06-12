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
        Schema::table('webinar_email_out', function (Blueprint $table) {
            $table->foreign('course_id', 'webinar_email_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('webinar_id', 'webinar_email_webinar_id')->references('id')->on('webinars')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webinar_email_out', function (Blueprint $table) {
            $table->dropForeign('webinar_email_course_id');
            $table->dropForeign('webinar_email_webinar_id');
        });
    }
};
