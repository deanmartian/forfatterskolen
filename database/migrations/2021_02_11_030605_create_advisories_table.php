<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdvisoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('advisories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('page_name');
			$table->text('page_included')->nullable();
			$table->text('advisory');
			$table->date('from_date')->nullable();
			$table->date('to_date')->nullable();
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
		Schema::drop('advisories');
	}

}
