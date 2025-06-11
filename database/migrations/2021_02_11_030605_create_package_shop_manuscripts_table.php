<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePackageShopManuscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_shop_manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned()->index('package_id');
            $table->integer('shop_manuscript_id')->unsigned()->index('shop_manuscript_id');
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
        Schema::drop('package_shop_manuscripts');
    }
}
