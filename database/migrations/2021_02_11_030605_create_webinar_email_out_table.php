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
        Schema::create('webinar_email_out', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('webinar_id')->unsigned()->index('webinar_id');
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->string('subject')->nullable();
            $table->date('send_date');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('webinar_email_out');
    }
};
