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
        Schema::create('orders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->unsigned()->index('orders_user');
            $table->integer('item_id');
            $table->boolean('type');
            $table->integer('package_id')->default(0);
            $table->integer('plan_id');
            $table->integer('payment_mode_id')->nullable();
            $table->decimal('price', 10)->nullable();
            $table->decimal('discount', 10)->nullable();
            $table->string('svea_order_id', 50)->nullable();
            $table->string('svea_invoice_id', 50)->nullable();
            $table->boolean('is_processed')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('orders');
    }
};
