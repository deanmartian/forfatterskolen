<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPrivateGroupDiscussionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('private_group_discussions', function(Blueprint $table)
		{
			$table->foreign('user_id', 'private_group_discussions_author_id_foreign')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('private_group_id')->references('id')->on('private_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('private_group_discussions', function(Blueprint $table)
		{
			$table->dropForeign('private_group_discussions_author_id_foreign');
			$table->dropForeign('private_group_discussions_private_group_id_foreign');
		});
	}

}
