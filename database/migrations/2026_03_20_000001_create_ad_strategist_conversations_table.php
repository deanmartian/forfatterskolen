<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_strategist_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('instruction');
            $table->json('campaign_context')->nullable();
            $table->json('ai_response')->nullable();
            $table->json('execution_results')->nullable();
            $table->string('status')->default('pending'); // pending, executed, failed
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_strategist_conversations');
    }
};
