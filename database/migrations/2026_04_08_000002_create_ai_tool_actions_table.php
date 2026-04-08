<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tool_actions', function (Blueprint $table) {
            $table->id();

            // Hva det gjelder
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('inbox_message_id')->nullable();

            // Selve verktøyet
            $table->string('tool_name', 100);
            $table->json('parameters');
            $table->string('ui_label', 255); // ferdigformulert tekst som vises på knappen

            // Status og tidsstempler
            $table->enum('status', ['suggested', 'executed', 'failed', 'skipped', 'expired'])
                ->default('suggested');
            $table->timestamp('suggested_at')->useCurrent();
            $table->timestamp('executed_at')->nullable();
            $table->unsignedBigInteger('executed_by_user_id')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Resultat eller feil
            $table->json('result')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index('conversation_id');
            $table->index('inbox_message_id');
            $table->index('tool_name');
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tool_actions');
    }
};
