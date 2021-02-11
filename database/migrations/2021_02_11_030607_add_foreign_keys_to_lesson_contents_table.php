<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLessonContentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('lesson_contents', function(Blueprint $table)
		{
			$table->foreign('lesson_id', 'lesson_contents_lesson_id')->references('id')->on('lessons')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('lesson_contents', function(Blueprint $table)
		{
			$table->dropForeign('lesson_contents_lesson_id');
		});
	}

}
