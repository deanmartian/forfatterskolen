<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebinarRegistrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_registrants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('webinar_id')->unsigned()->index('webinar_id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('join_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('webinar_registrants');
    }
}
