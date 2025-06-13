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
        Schema::table('user_auto_register_to_course_webinar', function (Blueprint $table) {
            $table->foreign('course_id', 'user_auto_register_to_course_webinar_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'user_auto_register_to_course_webinar_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_auto_register_to_course_webinar', function (Blueprint $table) {
            $table->dropForeign('user_auto_register_to_course_webinar_course_id');
            $table->dropForeign('user_auto_register_to_course_webinar_user_id');
        });
    }
};
