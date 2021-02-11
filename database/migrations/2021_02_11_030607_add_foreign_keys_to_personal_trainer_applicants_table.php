<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPersonalTrainerApplicantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('personal_trainer_applicants', function(Blueprint $table)
		{
			$table->foreign('user_id', 'pt_applicants_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('personal_trainer_applicants', function(Blueprint $table)
		{
			$table->dropForeign('pt_applicants_user_id_foreign');
		});
	}

}
