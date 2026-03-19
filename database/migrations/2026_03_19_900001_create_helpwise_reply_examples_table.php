<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpwise_reply_examples', function (Blueprint $table) {
            $table->id();
            $table->string('external_message_id')->unique();
            $table->string('conversation_id')->index();
            $table->string('subject')->nullable();
            $table->string('sender_email')->nullable();
            $table->longText('reply_body');
            $table->timestamp('sent_at')->nullable();
            $table->string('category')->nullable();
            $table->string('body_hash', 64)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpwise_reply_examples');
    }
};
