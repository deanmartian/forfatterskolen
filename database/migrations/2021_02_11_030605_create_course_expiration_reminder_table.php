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
        Schema::create('course_expiration_reminder', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->string('subject_28_days');
            $table->text('message_28_days');
            $table->string('subject_1_week');
            $table->text('message_1_week');
            $table->string('subject_1_day');
            $table->text('message_1_day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('course_expiration_reminder');
    }
};
