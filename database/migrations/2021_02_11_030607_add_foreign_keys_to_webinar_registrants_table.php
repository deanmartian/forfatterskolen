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
        Schema::table('webinar_registrants', function (Blueprint $table) {
            $table->foreign('user_id', 'registrants_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('webinar_id', 'registrants_webinar_id')->references('id')->on('webinars')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webinar_registrants', function (Blueprint $table) {
            $table->dropForeign('registrants_user_id');
            $table->dropForeign('registrants_webinar_id');
        });
    }
};
