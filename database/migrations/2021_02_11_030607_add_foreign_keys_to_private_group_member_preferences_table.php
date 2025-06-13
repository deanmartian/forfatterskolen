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
        Schema::table('private_group_member_preferences', function (Blueprint $table) {
            $table->foreign('user_id', 'private_group_member_preferences_author_id_foreign')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('private_group_id')->references('id')->on('private_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_group_member_preferences', function (Blueprint $table) {
            $table->dropForeign('private_group_member_preferences_author_id_foreign');
            $table->dropForeign('private_group_member_preferences_private_group_id_foreign');
        });
    }
};
