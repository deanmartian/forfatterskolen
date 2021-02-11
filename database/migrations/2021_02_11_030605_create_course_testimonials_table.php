<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseTestimonialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_testimonials', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('course_id')->unsigned()->index('FK_course_testimonials_courses');
			$table->string('name');
			$table->text('testimony', 65535)->nullable();
			$table->string('user_image')->nullable();
			$table->boolean('is_video')->default(0);
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
		Schema::drop('course_testimonials');
	}

}
