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
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->foreign('editor_id', 'free_manuscripts_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->dropForeign('free_manuscripts_ibfk_1');
        });
    }
};
