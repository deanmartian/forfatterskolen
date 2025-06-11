<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->foreign('user_id', 'shop_manuscripts_taken_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('shop_manuscript_id', 'shop_manuscripts_taken_ibfk_2')->references('id')->on('shop_manuscripts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('feedback_user_id', 'shop_manuscripts_taken_ibfk_3')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
            $table->dropForeign('shop_manuscripts_taken_ibfk_1');
            $table->dropForeign('shop_manuscripts_taken_ibfk_2');
            $table->dropForeign('shop_manuscripts_taken_ibfk_3');
        });
    }
};
