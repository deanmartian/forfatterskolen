<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOtherServiceFeedbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('other_service_feedbacks', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('service_id');
			$table->integer('service_type');
			$table->string('manuscript');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('other_service_feedbacks');
	}

}
