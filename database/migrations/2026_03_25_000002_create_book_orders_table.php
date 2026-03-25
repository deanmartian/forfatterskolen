<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->json('items');
            $table->integer('subtotal');
            $table->integer('shipping_cost')->default(0);
            $table->integer('total');

            // Kunde
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20)->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_zip', 10)->nullable();
            $table->string('shipping_country', 2)->default('NO');

            // Betaling
            $table->string('payment_method', 20)->nullable();
            $table->string('payment_status', 20)->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Fiken
            $table->unsignedBigInteger('fiken_invoice_id')->nullable();
            $table->string('fiken_invoice_number', 30)->nullable();

            // Levering
            $table->string('fulfillment_status', 20)->default('pending');
            $table->string('tracking_number', 50)->nullable();
            $table->timestamp('shipped_at')->nullable();

            // E-bok
            $table->string('download_token', 64)->nullable()->unique();
            $table->integer('download_count')->default(0);
            $table->timestamp('download_expires_at')->nullable();

            // Admin-notater
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index('payment_status');
            $table->index('fulfillment_status');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_orders');
    }
};
