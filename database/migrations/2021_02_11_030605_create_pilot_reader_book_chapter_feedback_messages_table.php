<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePilotReaderBookChapterFeedbackMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pilot_reader_book_chapter_feedback_messages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('feedback_id')->index('feedback_id');
			$table->text('message', 65535)->nullable();
			$table->string('mark')->default('unmarked');
			$table->boolean('published');
			$table->boolean('is_reply')->default(0);
			$table->integer('reply_from')->default(0);
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
		Schema::drop('pilot_reader_book_chapter_feedback_messages');
	}

}
