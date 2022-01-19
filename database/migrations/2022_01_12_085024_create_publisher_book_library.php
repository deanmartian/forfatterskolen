<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublisherBookLibrary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_book_library', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('publisher_book_id');
            $table->string('book_image');
            $table->string('book_link')->nullable();
            $table->timestamps();

            $table->foreign('publisher_book_id')->references('id')->on('publisher_books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('publisher_book_library');
    }
}
