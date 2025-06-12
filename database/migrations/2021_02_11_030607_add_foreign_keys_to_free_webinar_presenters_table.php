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
        Schema::table('free_webinar_presenters', function (Blueprint $table) {
            $table->foreign('free_webinar_id', 'free_webinar_presenters_ibfk_1')->references('id')->on('free_webinars')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_webinar_presenters', function (Blueprint $table) {
            $table->dropForeign('free_webinar_presenters_ibfk_1');
        });
    }
};
