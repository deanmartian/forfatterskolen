<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFreeCourseDelayedEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_course_delayed_email', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->dateTime('send_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('free_course_delayed_email');
    }
}
