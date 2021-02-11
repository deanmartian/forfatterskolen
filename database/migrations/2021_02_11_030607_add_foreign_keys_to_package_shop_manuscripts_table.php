<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPackageShopManuscriptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('package_shop_manuscripts', function(Blueprint $table)
		{
			$table->foreign('package_id', 'package_shop_manuscripts_ibfk_1')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('shop_manuscript_id', 'package_shop_manuscripts_ibfk_2')->references('id')->on('shop_manuscripts')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('package_shop_manuscripts', function(Blueprint $table)
		{
			$table->dropForeign('package_shop_manuscripts_ibfk_1');
			$table->dropForeign('package_shop_manuscripts_ibfk_2');
		});
	}

}
