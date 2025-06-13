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
        Schema::table('learner_emails', function (Blueprint $table) {
            $table->foreign('user_id', 'FK_learner_emails_users')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learner_emails', function (Blueprint $table) {
            $table->dropForeign('FK_learner_emails_users');
        });
    }
};
