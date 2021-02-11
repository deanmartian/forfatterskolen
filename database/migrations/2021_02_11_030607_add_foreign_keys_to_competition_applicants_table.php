<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCompetitionApplicantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('competition_applicants', function(Blueprint $table)
		{
			$table->foreign('user_id', 'competition_applicant_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('competition_applicants', function(Blueprint $table)
		{
			$table->dropForeign('competition_applicant_user_id');
		});
	}

}
