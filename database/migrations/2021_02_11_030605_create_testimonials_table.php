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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->string('description');
            $table->text('testimony', 65535);
            $table->string('author_image');
            $table->string('book_image');
            $table->boolean('status');
            $table->dateTime('created_at')->nullable();
            $table->text('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('testimonials');
    }
};
