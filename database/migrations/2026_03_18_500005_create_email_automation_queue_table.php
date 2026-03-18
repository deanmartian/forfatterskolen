<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_automation_queue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->string('email'); // Denormalisert for pålitelighet
            $table->unsignedBigInteger('sequence_id');
            $table->unsignedBigInteger('step_id');
            $table->dateTime('scheduled_at');
            $table->dateTime('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, cancelled, failed
            $table->string('cancelled_reason')->nullable();
            $table->json('metadata')->nullable(); // webinar_id, etc.
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index('contact_id');

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('sequence_id')->references('id')->on('email_sequences')->onDelete('cascade');
            $table->foreign('step_id')->references('id')->on('email_sequence_steps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_automation_queue');
    }
};
