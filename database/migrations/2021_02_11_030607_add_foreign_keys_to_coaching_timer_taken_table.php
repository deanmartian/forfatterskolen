<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCoachingTimerTakenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coaching_timer_taken', function (Blueprint $table) {
            $table->foreign('course_taken_id', 'coaching_timer_taken_course_taken_id')->references('id')->on('courses_taken')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'coaching_timer_taken_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coaching_timer_taken', function (Blueprint $table) {
            $table->dropForeign('coaching_timer_taken_course_taken_id');
            $table->dropForeign('coaching_timer_taken_user_id');
        });
    }
}
