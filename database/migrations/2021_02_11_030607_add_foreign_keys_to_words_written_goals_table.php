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
        Schema::table('words_written_goals', function (Blueprint $table) {
            $table->foreign('user_id', 'words_written_goal_user_id_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('words_written_goals', function (Blueprint $table) {
            $table->dropForeign('words_written_goal_user_id_ibfk_1');
        });
    }
};
