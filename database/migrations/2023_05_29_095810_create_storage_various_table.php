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
        Schema::create('storage_various', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_book_for_sale_id');
            $table->string('publisher')->nullable();
            $table->string('minimum_stock')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('thickness')->nullable();
            $table->string('cost')->nullable();
            $table->string('material_cost')->nullable();
            $table->timestamps();

            $table->foreign('user_book_for_sale_id')->references('id')->on('user_books_for_sale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_various');
    }
};
