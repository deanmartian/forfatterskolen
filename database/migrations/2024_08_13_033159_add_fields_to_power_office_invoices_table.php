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
        Schema::table('power_office_invoices', function (Blueprint $table) {
            $table->string('sales_order_no')->nullable()->after('order_id');
            $table->string('invoice_id')->nullable()->after('sales_order_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_office_invoices', function (Blueprint $table) {
            $table->dropColumn('sales_order_no');
            $table->dropColumn('invoice_id');
        });
    }
};
