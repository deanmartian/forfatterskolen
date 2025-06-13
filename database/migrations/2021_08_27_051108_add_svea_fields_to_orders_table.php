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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('svea_payment_type')->nullable()->after('svea_invoice_id');
            $table->string('svea_payment_type_description')->nullable()->after('svea_payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('svea_payment_type');
            $table->dropColumn('svea_payment_type_description');
        });
    }
};
