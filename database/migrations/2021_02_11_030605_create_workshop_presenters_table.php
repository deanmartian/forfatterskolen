<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkshopPresentersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workshop_presenters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('workshop_id')->unsigned()->index('workshop_id');
			$table->string('first_name', 100)->default('');
			$table->string('last_name', 100)->default('');
			$table->string('email', 100)->default('');
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
		Schema::drop('workshop_presenters');
	}

}
