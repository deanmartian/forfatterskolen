<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkshopMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('workshop_menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('workshop_id')->unsigned()->index('workshop_id');
			$table->string('title', 100)->default('');
			$table->text('description');
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
		Schema::drop('workshop_menus');
	}

}
