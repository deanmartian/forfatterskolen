<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesEmailOutRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses_email_out_recipients', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('email_out_id');
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('email_out_id')->references('id')->on('courses_email_out')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses_email_out_recipients');
    }
}
