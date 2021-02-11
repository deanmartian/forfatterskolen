<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCorrectionManuscriptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('correction_manuscripts', function(Blueprint $table)
		{
			$table->foreign('editor_id', 'correction_manuscripts_editor')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('correction_manuscripts', function(Blueprint $table)
		{
			$table->dropForeign('correction_manuscripts_editor');
		});
	}

}
