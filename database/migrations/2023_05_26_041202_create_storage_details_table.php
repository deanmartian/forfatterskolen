<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('storage_book_id');
            $table->string('subtitle');
            $table->string('original_title')->nullable();
            $table->string('author');
            $table->string('editor');
            $table->string('publisher');
            $table->string('book_group');
            $table->string('item_number');
            $table->string('isbn');
            $table->string('isbn_ebook');
            $table->integer('edition_on_sale');
            $table->integer('edition_total');
            $table->date('release_date');
            $table->integer('price_vat');
            $table->string('registered_with_council');
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
        Schema::dropIfExists('storage_details');
    }
}
