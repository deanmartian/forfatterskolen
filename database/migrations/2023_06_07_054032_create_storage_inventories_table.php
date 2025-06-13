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
        Schema::create('storage_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_book_for_sale_id');
            $table->integer('total')->nullable();
            $table->integer('delivered')->nullable();
            $table->integer('physical_items')->nullable();
            $table->integer('returns')->nullable();
            $table->integer('balance')->nullable();
            $table->integer('order')->nullable();
            $table->integer('reservations')->nullable();
            $table->timestamps();

            $table->foreign('user_book_for_sale_id')->references('id')->on('user_books_for_sale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_inventories');
    }
};
