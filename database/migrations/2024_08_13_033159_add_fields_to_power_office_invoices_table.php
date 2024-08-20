<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPowerOfficeInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('power_office_invoices', function (Blueprint $table) {
            $table->string('sales_order_no')->nullable()->after('order_id');
            $table->string('invoice_id')->nullable()->after('sales_order_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('power_office_invoices', function (Blueprint $table) {
            $table->dropColumn('sales_order_no');
            $table->dropColumn('invoice_id');
        });
    }
}
