<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorageVariousTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_various', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('storage_book_id');
            $table->string('publisher');
            $table->string('minimum_stock');
            $table->string('weight');
            $table->string('height');
            $table->string('width');
            $table->string('thickness');
            $table->string('cost');
            $table->string('material_cost');
            $table->timestamps();

            $table->foreign('storage_book_id')->references('id')->on('storage_books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_various');
    }
}
