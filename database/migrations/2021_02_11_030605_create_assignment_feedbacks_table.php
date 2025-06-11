<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssignmentFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assignment_group_learner_id')->unsigned()->index('assignment_group_learner_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('filename')->default('');
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_active')->default(0);
            $table->date('availability')->nullable();
            $table->boolean('locked')->nullable()->default(0);
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
        Schema::drop('assignment_feedbacks');
    }
}
