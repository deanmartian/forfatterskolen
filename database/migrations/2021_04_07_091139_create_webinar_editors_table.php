<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebinarEditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_editors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('editor_id');
            $table->unsignedInteger('webinar_id');
            $table->string('presenter_url', 1000)->nullable();
            $table->timestamps();

            $table->foreign('editor_id')->references('id')->on('users');
            $table->foreign('webinar_id')->references('id')->on('webinars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_editors');
    }
}
