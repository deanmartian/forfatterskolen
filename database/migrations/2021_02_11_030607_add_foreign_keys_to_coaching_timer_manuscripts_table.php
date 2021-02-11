<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCoachingTimerManuscriptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('coaching_timer_manuscripts', function(Blueprint $table)
		{
			$table->foreign('editor_id', 'coaching_timer_manuscripts_editor')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'coaching_timer_manuscripts_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('coaching_timer_manuscripts', function(Blueprint $table)
		{
			$table->dropForeign('coaching_timer_manuscripts_editor');
			$table->dropForeign('coaching_timer_manuscripts_user_id');
		});
	}

}
