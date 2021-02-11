<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCalendarNoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('calendar_note', function(Blueprint $table)
		{
			$table->foreign('course_id', 'FK_calendar_note_courses')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('calendar_note', function(Blueprint $table)
		{
			$table->dropForeign('FK_calendar_note_courses');
		});
	}

}
