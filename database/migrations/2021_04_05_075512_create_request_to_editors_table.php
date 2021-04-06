<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestToEditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_to_editors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_type');
            $table->unsignedInteger('editor_id');
            $table->unsignedInteger('manuscript_id');
            $table->date('answer_until');
            $table->string('answer')->nullable();
            $table->timestamps();

            $table->foreign('editor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_to_editors');
    }
}
