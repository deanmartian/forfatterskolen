<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_history', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('subject');
			$table->string('from_email');
			$table->text('message');
			$table->string('parent');
			$table->integer('parent_id');
			$table->string('recipient', 100)->nullable();
			$table->string('track_code', 100)->nullable();
			$table->dateTime('date_open')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_history');
	}

}
