<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFreeWebinarPresentersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('free_webinar_presenters', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('free_webinar_id')->index('workshop_id');
			$table->string('first_name', 100);
			$table->string('last_name', 100);
			$table->string('email', 100);
			$table->string('image')->nullable();
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
		Schema::drop('free_webinar_presenters');
	}

}
