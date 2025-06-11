<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLearnerEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('learner_emails', function (Blueprint $table) {
            $table->foreign('user_id', 'FK_learner_emails_users')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('learner_emails', function (Blueprint $table) {
            $table->dropForeign('FK_learner_emails_users');
        });
    }
}
