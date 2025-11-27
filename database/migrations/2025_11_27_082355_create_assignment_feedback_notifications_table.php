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
        Schema::create('assignment_feedback_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('assignment_feedback_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('availability');
            $table->tinyInteger('is_read');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_feedback_notifications');
    }
};
