<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('free_webinar_id');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('join_url')->nullable();
            $table->boolean('confirmation_sent')->default(false);
            $table->boolean('reminder_day_before_sent')->default(false);
            $table->boolean('reminder_hour_before_sent')->default(false);
            $table->timestamps();

            $table->unique(['free_webinar_id', 'email']);
            $table->foreign('free_webinar_id')->references('id')->on('free_webinars')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_registrations');
    }
};
