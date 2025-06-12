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
        Schema::table('diplomas', function (Blueprint $table) {
            $table->foreign('course_id', 'diploma_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'diploma_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diplomas', function (Blueprint $table) {
            $table->dropForeign('diploma_course_id');
            $table->dropForeign('diploma_user_id');
        });
    }
};
