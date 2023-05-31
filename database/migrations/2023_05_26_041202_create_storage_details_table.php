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
            $table->string('subtitle')->nullable();
            $table->string('original_title')->nullable();
            $table->string('author')->nullable();
            $table->string('editor')->nullable();
            $table->string('publisher')->nullable();
            $table->string('book_group')->nullable();
            $table->string('item_number')->nullable();
            $table->string('isbn')->nullable();
            $table->string('isbn_ebook')->nullable();
            $table->integer('edition_on_sale')->nullable();
            $table->integer('edition_total')->nullable();
            $table->date('release_date')->nullable();
            $table->date('release_date_for_media')->nullable();
            $table->integer('price_vat')->nullable();
            $table->string('registered_with_council')->nullable();
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
