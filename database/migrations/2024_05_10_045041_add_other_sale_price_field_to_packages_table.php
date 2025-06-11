<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('full_payment_other_sale_price')->nullable()->after('full_payment_sale_price_to');
            $table->date('full_payment_other_sale_price_from')->nullable()->after('full_payment_other_sale_price');
            $table->date('full_payment_other_sale_price_to')->nullable()->after('full_payment_other_sale_price_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('full_payment_other_sale_price');
            $table->dropColumn('full_payment_other_sale_price_from');
            $table->dropColumn('full_payment_other_sale_price_to');
        });
    }
};
