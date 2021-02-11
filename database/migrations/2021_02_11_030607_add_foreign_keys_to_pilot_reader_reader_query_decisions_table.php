<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPilotReaderReaderQueryDecisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pilot_reader_reader_query_decisions', function(Blueprint $table)
		{
			$table->foreign('query_id', 'reader_query_decisions_query_id_foreign')->references('id')->on('pilot_reader_reader_queries')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pilot_reader_reader_query_decisions', function(Blueprint $table)
		{
			$table->dropForeign('reader_query_decisions_query_id_foreign');
		});
	}

}
