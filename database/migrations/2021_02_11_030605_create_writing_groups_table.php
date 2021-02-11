<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWritingGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('writing_groups', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('contact_id')->unsigned()->index('contact_id');
			$table->text('name', 65535);
			$table->text('description');
			$table->string('group_photo')->nullable();
			$table->text('next_meeting', 65535)->nullable();
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
		Schema::drop('writing_groups');
	}

}
