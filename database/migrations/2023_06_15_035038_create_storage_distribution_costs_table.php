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
        Schema::create('storage_distribution_costs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_book_for_sale_id');
            $table->string('nr');
            $table->string('service');
            $table->integer('number');
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('user_book_for_sale_id')->references('id')->on('user_books_for_sale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_distribution_costs');
    }
};
