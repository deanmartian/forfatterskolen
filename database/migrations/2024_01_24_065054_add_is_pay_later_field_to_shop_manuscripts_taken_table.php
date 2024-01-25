<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPayLaterFieldToShopManuscriptsTakenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->tinyInteger('is_pay_later')->after('is_welcome_email_sent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->dropColumn('is_pay_later');
        });
    }
}
