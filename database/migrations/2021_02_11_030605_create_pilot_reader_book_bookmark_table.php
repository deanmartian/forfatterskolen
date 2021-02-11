<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePilotReaderBookBookmarkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pilot_reader_book_bookmark', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('bookmarker_id')->index('bookmarker_id');
			$table->integer('book_id')->index('book_id');
			$table->integer('chapter_id')->index('chapter_id');
			$table->text('paragraph_text', 65535);
			$table->integer('paragraph_order');
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
		Schema::drop('pilot_reader_book_bookmark');
	}

}
