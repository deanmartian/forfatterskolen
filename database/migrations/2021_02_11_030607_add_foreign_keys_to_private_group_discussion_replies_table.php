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
        Schema::table('private_group_discussion_replies', function (Blueprint $table) {
            $table->foreign('disc_id', 'private_group_discussion_replies_author_id_foreign')->references('id')->on('private_group_discussions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'private_group_discussion_replies_disc_id_foreign')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_group_discussion_replies', function (Blueprint $table) {
            $table->dropForeign('private_group_discussion_replies_author_id_foreign');
            $table->dropForeign('private_group_discussion_replies_disc_id_foreign');
        });
    }
};
