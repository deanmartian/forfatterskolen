<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_recording_logs', function (Blueprint $table) {
            $table->id();
            $table->string('webinar_title');
            $table->unsignedBigInteger('webinar_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('course_name')->nullable();
            $table->string('bigmarker_id')->nullable();
            $table->string('wistia_id')->nullable();
            $table->string('wistia_project')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('lesson_title')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedBigInteger('lesson_content_id')->nullable();
            $table->enum('status', ['success', 'failed', 'skipped'])->default('success');
            $table->text('error_message')->nullable();
            $table->integer('file_size_mb')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_recording_logs');
    }
};
