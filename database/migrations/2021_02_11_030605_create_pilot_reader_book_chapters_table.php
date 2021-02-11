<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePilotReaderBookChaptersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pilot_reader_book_chapters', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('pilot_reader_book_id')->index('book_id');
			$table->string('title')->nullable();
			$table->text('pre_read_guidance', 65535)->nullable();
			$table->text('post_read_guidance', 65535)->nullable();
			$table->boolean('notify_readers')->default(0);
			$table->integer('word_count')->default(0);
			$table->integer('display_order')->default(0);
			$table->boolean('is_hidden')->default(0);
			$table->boolean('type')->default(1);
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
		Schema::drop('pilot_reader_book_chapters');
	}

}
