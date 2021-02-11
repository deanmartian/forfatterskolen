<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrivateGroupDiscussionRepliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('private_group_discussion_replies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('disc_id')->unsigned()->index('private_group_discussion_replies_disc_id_foreign');
			$table->integer('user_id')->unsigned()->index('private_group_discussion_replies_author_id_foreign');
			$table->string('message', 150)->nullable();
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
		Schema::drop('private_group_discussion_replies');
	}

}
