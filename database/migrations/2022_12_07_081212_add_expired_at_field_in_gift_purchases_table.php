<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiredAtFieldInGiftPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_purchases', function (Blueprint $table) {
            $table->timestamp('expired_at')->nullable()->after('is_redeemed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_purchases', function (Blueprint $table) {
            $table->dropColumn('expired_at');
        });
    }
}
