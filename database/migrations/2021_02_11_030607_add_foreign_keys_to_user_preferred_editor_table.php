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
        Schema::table('user_preferred_editor', function (Blueprint $table) {
            $table->foreign('editor_id', 'user_preferred_editor_editor_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'user_preferred_editor_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_preferred_editor', function (Blueprint $table) {
            $table->dropForeign('user_preferred_editor_editor_id');
            $table->dropForeign('user_preferred_editor_user_id');
        });
    }
};
