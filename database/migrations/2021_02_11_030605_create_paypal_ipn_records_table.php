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
        Schema::create('paypal_ipn_records', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('invoice_id')->nullable();
            $table->string('verified');
            $table->string('transaction_id');
            $table->string('payment_status');
            $table->string('request_method')->nullable();
            $table->string('request_url')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('paypal_ipn_records');
    }
};
