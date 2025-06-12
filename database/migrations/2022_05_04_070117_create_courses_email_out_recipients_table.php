<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
     */
    public function down(): void
    {
        Schema::dropIfExists('courses_email_out_recipients');
    }
};
