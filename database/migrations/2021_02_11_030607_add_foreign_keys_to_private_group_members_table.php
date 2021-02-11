<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPrivateGroupMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('private_group_members', function(Blueprint $table)
		{
			$table->foreign('private_group_id')->references('id')->on('private_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'private_group_members_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('private_group_members', function(Blueprint $table)
		{
			$table->dropForeign('private_group_members_private_group_id_foreign');
			$table->dropForeign('private_group_members_user_id');
		});
	}

}
