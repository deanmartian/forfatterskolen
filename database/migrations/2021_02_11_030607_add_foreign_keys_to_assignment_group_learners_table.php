<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAssignmentGroupLearnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_group_learners', function (Blueprint $table) {
            $table->foreign('user_id', 'assignment_group_learners_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('assignment_group_id', 'assignment_group_learners_ibfk_3')->references('id')->on('assignment_groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_group_learners', function (Blueprint $table) {
            $table->dropForeign('assignment_group_learners_ibfk_2');
            $table->dropForeign('assignment_group_learners_ibfk_3');
        });
    }
}
