<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFreeCourseDelayedEmailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('free_course_delayed_email', function(Blueprint $table)
		{
			$table->foreign('course_id', 'delayed_email_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'delayed_email_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('free_course_delayed_email', function(Blueprint $table)
		{
			$table->dropForeign('delayed_email_course_id');
			$table->dropForeign('delayed_email_user_id');
		});
	}

}
