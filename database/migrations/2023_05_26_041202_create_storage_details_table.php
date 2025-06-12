<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_book_for_sale_id');
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

            $table->foreign('user_book_for_sale_id')->references('id')->on('user_books_for_sale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_details');
    }
};
