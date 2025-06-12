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
        Schema::table('user_renewed_course', function (Blueprint $table) {
            $table->foreign('course_id', 'user_renewed_course_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'user_renewed_course_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_renewed_course', function (Blueprint $table) {
            $table->dropForeign('user_renewed_course_course_id');
            $table->dropForeign('user_renewed_course_user_id');
        });
    }
};
