<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCopyEditingManuscriptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('copy_editing_manuscripts', function(Blueprint $table)
		{
			$table->foreign('editor_id', 'copy_editing_manuscripts_editor')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('copy_editing_manuscripts', function(Blueprint $table)
		{
			$table->dropForeign('copy_editing_manuscripts_editor');
		});
	}

}
