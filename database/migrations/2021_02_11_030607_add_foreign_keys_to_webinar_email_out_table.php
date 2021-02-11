<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWebinarEmailOutTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('webinar_email_out', function(Blueprint $table)
		{
			$table->foreign('course_id', 'webinar_email_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('webinar_id', 'webinar_email_webinar_id')->references('id')->on('webinars')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('webinar_email_out', function(Blueprint $table)
		{
			$table->dropForeign('webinar_email_course_id');
			$table->dropForeign('webinar_email_webinar_id');
		});
	}

}
