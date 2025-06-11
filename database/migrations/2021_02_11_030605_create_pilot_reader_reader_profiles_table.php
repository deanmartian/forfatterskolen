<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePilotReaderReaderProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pilot_reader_reader_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('genre_preferences', 65535)->nullable();
            $table->text('dislike_contents', 65535)->nullable();
            $table->text('expertise', 65535)->nullable();
            $table->text('favourite_author', 65535)->nullable();
            $table->boolean('availability')->default(0);
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
        Schema::drop('pilot_reader_reader_profiles');
    }
}
