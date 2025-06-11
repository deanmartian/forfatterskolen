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
        Schema::create('pilot_reader_book_invitation', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('book_id')->index('book_id');
            $table->string('email');
            $table->integer('send_count')->default(1);
            $table->boolean('status')->default(0);
            $table->string('_token');
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
        Schema::drop('pilot_reader_book_invitation');
    }
};
