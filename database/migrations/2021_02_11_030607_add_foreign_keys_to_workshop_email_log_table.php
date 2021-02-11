<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWorkshopEmailLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('workshop_email_log', function(Blueprint $table)
		{
			$table->foreign('workshop_id', 'workshop_email_log_workshop_id')->references('id')->on('workshops')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('workshop_email_log', function(Blueprint $table)
		{
			$table->dropForeign('workshop_email_log_workshop_id');
		});
	}

}
