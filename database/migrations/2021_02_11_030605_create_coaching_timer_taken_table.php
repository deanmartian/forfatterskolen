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
        Schema::create('coaching_timer_taken', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('coaching_timer_taken_user_id');
            $table->integer('course_taken_id')->unsigned()->index('coaching_timer_taken_course_taken_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('coaching_timer_taken');
    }
};
