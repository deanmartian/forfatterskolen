<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSosChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sos_children', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title');
            $table->text('description');
            $table->text('bottom_description')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_main_description')->default(0);
            $table->boolean('is_primary')->default(0);
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
        Schema::drop('sos_children');
    }
}
