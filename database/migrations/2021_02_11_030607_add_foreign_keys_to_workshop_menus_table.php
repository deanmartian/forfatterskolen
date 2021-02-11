<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWorkshopMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('workshop_menus', function(Blueprint $table)
		{
			$table->foreign('workshop_id', 'workshop_menus_ibfk_1')->references('id')->on('workshops')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('workshop_menus', function(Blueprint $table)
		{
			$table->dropForeign('workshop_menus_ibfk_1');
		});
	}

}
