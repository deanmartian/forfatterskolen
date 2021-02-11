<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSolutionArticlesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('solution_articles', function(Blueprint $table)
		{
			$table->foreign('solution_id', 'solution_id')->references('id')->on('solutions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('solution_articles', function(Blueprint $table)
		{
			$table->dropForeign('solution_id');
		});
	}

}
