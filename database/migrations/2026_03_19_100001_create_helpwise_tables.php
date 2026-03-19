<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('helpwise_conversations')) {
            Schema::create('helpwise_conversations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('helpwise_id')->unique()->index();
                $table->string('inbox')->nullable();
                $table->string('inbox_id')->nullable();
                $table->string('subject')->nullable();
                $table->string('customer_email')->nullable()->index();
                $table->string('customer_name')->nullable();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->enum('status', ['open', 'closed', 'pending', 'snoozed', 'unknown'])->default('open');
                $table->string('assigned_to')->nullable();
                $table->json('tags')->nullable();
                $table->json('raw_payload')->nullable();
                $table->timestamp('helpwise_created_at')->nullable();
                $table->timestamp('helpwise_closed_at')->nullable();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('helpwise_messages')) {
            Schema::create('helpwise_messages', function (Blueprint $table) {
                $table->increments('id');
                $table->string('helpwise_message_id')->nullable()->index();
                $table->unsignedInteger('conversation_id')->index();
                $table->enum('direction', ['inbound', 'outbound'])->default('inbound');
                $table->string('from_email')->nullable();
                $table->string('from_name')->nullable();
                $table->string('to_email')->nullable();
                $table->text('subject')->nullable();
                $table->longText('body')->nullable();
                $table->text('body_plain')->nullable();
                $table->json('attachments')->nullable();
                $table->string('channel')->nullable();
                $table->json('raw_payload')->nullable();
                $table->timestamp('message_at')->nullable();
                $table->timestamps();
                $table->foreign('conversation_id')->references('id')->on('helpwise_conversations')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('helpwise_webhook_logs')) {
            Schema::create('helpwise_webhook_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('event_type')->index();
                $table->json('payload');
                $table->enum('status', ['received', 'processed', 'failed'])->default('received');
                $table->text('error_message')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('helpwise_webhook_logs');
        Schema::dropIfExists('helpwise_messages');
        Schema::dropIfExists('helpwise_conversations');
    }
};
