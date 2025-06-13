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
        Schema::create('order_coaching_timer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('additional_price');
            $table->string('file')->nullable();
            $table->string('suggested_date')->nullable();
            $table->longText('help_with')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_coaching_timer');
    }
};
