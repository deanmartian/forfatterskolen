<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publisher_books', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title');
            $table->text('description', 65535);
            $table->text('quote_description', 65535)->nullable();
            $table->string('author_image');
            $table->string('book_image');
            $table->string('book_image_link')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('publisher_books');
    }
};
