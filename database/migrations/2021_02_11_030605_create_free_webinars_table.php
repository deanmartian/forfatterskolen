<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFreeWebinarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('free_webinars', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title');
			$table->text('description');
			$table->dateTime('start_date');
			$table->string('image')->nullable();
			$table->string('gtwebinar_id');
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
		Schema::drop('free_webinars');
	}

}
