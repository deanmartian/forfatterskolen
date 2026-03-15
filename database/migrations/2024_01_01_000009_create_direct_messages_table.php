<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('recipient_id');
            $table->text('content');
            $table->boolean('read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('sender_id');
            $table->index('recipient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_messages');
    }
};
