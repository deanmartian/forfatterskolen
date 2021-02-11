<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLessonsDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lessons_documents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('lesson_id')->unsigned()->index('FK_lessons_documents_lessons');
			$table->string('name');
			$table->string('document');
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
		Schema::drop('lessons_documents');
	}

}
