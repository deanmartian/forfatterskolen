<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectBookFormattingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_book_formatting', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('project_id');
            $table->longText('file');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_book_formatting');
    }
}
