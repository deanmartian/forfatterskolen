<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebinarScheduledRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webinar_scheduled_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('webinar_id');
            $table->date('date');
            $table->timestamps();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webinar_scheduled_registrations');
    }
}
