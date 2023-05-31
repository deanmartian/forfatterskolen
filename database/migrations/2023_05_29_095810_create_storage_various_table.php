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
            $table->string('publisher')->nullable();
            $table->string('minimum_stock')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('thickness')->nullable();
            $table->string('cost')->nullable();
            $table->string('material_cost')->nullable();
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
