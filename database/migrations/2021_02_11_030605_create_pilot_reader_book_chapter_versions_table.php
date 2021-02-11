<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePilotReaderBookChapterVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pilot_reader_book_chapter_versions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('chapter_id')->index('pilot_reader_book_chapter_versions_chapter_id');
			$table->text('content', 16777215)->nullable();
			$table->text('change_description', 65535)->nullable();
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
		Schema::drop('pilot_reader_book_chapter_versions');
	}

}
