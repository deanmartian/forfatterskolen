<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpwise_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique()->nullable();
            $table->string('conversation_id')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('event_type')->nullable();
            $table->boolean('should_reply')->default(false);
            $table->decimal('confidence', 5, 2)->nullable();
            $table->string('draft_status')->default('pending'); // pending, created, failed, skipped
            $table->text('error_message')->nullable();
            $table->json('ai_response')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpwise_webhook_logs');
    }
};
