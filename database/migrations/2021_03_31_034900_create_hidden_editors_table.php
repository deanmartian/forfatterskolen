<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHiddenEditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hidden_editors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('editor_id');
            $table->date('hide_date_from');
            $table->date('hide_date_to')->nullable();
            $table->string('notes', 1000)->nullable();
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
        Schema::dropIfExists('hidden_editors');
    }
}
