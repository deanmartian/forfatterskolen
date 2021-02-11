<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSimilarCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('similar_courses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('course_id')->unsigned()->index('course_id');
			$table->integer('similar_course_id')->unsigned()->index('similar_course_id');
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
		Schema::drop('similar_courses');
	}

}
