<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssignmentGroupLearnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_group_learners', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assignment_group_id')->unsigned()->index('assignment_group_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assignment_group_learners');
    }
}
