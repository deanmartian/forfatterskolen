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
        Schema::create('shop_manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->default('');
            $table->text('description')->nullable();
            $table->integer('max_words');
            $table->decimal('full_payment_price', 11);
            $table->decimal('months_3_price', 11);
            $table->decimal('months_6_price', 11);
            $table->decimal('upgrade_price', 11);
            $table->string('fiken_product');
            $table->string('full_price_product', 100)->default('');
            $table->string('months_3_product', 100)->default('');
            $table->string('months_6_product', 100)->default('');
            $table->integer('full_price_due_date');
            $table->integer('months_3_due_date');
            $table->integer('months_6_due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('shop_manuscripts');
    }
};
