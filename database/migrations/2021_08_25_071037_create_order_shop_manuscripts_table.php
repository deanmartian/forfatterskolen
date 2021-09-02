<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderShopManuscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shop_manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->tinyInteger('genre');
            $table->longText('file');
            $table->integer('words');
            $table->longText('description')->nullable();
            $table->longText('synopsis')->nullable();
            $table->tinyInteger('coaching_time_later')->default(0);
            $table->tinyInteger('send_to_email')->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_shop_manuscripts');
    }
}
