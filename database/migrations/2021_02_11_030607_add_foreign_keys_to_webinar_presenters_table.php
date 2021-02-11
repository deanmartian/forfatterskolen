<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWebinarPresentersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('webinar_presenters', function(Blueprint $table)
		{
			$table->foreign('webinar_id', 'webinar_presenters_ibfk_1')->references('id')->on('webinars')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('webinar_presenters', function(Blueprint $table)
		{
			$table->dropForeign('webinar_presenters_ibfk_1');
		});
	}

}
