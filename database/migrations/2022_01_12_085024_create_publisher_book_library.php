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
        Schema::create('publisher_book_library', function (Blueprint $table) {
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
     */
    public function down(): void
    {
        Schema::drop('publisher_book_library');
    }
};
