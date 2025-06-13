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
        Schema::table('courses_shared_users', function (Blueprint $table) {
            $table->foreign('user_id', 'shared_user_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('course_shared_id', 'shared_users_shared_id')->references('id')->on('courses_shared')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_shared_users', function (Blueprint $table) {
            $table->dropForeign('shared_user_user_id');
            $table->dropForeign('shared_users_shared_id');
        });
    }
};
