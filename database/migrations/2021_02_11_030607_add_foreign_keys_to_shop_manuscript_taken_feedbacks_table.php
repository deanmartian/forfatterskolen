<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToShopManuscriptTakenFeedbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('shop_manuscript_taken_feedbacks', function(Blueprint $table)
		{
			$table->foreign('shop_manuscript_taken_id', 'shop_manuscript_taken_feedbacks_ibfk_1')->references('id')->on('shop_manuscripts_taken')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('shop_manuscript_taken_feedbacks', function(Blueprint $table)
		{
			$table->dropForeign('shop_manuscript_taken_feedbacks_ibfk_1');
		});
	}

}
