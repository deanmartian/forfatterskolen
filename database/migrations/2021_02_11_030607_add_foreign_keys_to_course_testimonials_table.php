<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourseTestimonialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_testimonials', function(Blueprint $table)
		{
			$table->foreign('course_id', 'FK_course_testimonials_courses')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('course_testimonials', function(Blueprint $table)
		{
			$table->dropForeign('FK_course_testimonials_courses');
		});
	}

}
