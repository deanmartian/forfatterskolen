<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrivateGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('private_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 150)->unique();
			$table->boolean('policy')->default(1);
			$table->text('welcome_msg', 65535)->nullable();
			$table->string('contact_email', 150)->nullable()->unique();
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
		Schema::drop('private_groups');
	}

}
