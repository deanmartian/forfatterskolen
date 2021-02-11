<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourseExpirationReminderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_expiration_reminder', function(Blueprint $table)
		{
			$table->foreign('course_id', ' course_expiration_reminder_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('course_expiration_reminder', function(Blueprint $table)
		{
			$table->dropForeign(' course_expiration_reminder_course_id');
		});
	}

}
