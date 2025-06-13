<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webinars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->string('title')->default('');
            $table->text('description');
            $table->dateTime('start_date');
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->boolean('set_as_replay')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('webinars');
    }
};
