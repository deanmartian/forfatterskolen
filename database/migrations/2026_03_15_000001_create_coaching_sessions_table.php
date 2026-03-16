<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaching_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('coaching_timer_manuscript_id');
            $table->unsignedBigInteger('editor_id');
            $table->unsignedBigInteger('student_id');
            $table->string('whereby_room_url')->nullable();
            $table->string('whereby_host_url', 500)->nullable();
            $table->string('whereby_meeting_id')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, active, completed
            $table->string('recording_path')->nullable();
            $table->longText('transcription')->nullable();
            $table->longText('summary')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('coaching_timer_manuscript_id');
            $table->index('editor_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_sessions');
    }
};
