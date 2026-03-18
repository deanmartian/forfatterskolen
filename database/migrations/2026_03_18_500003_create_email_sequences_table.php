<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_event'); // webinar_registration, facebook_lead, etc.
            $table->text('description')->nullable();
            $table->string('from_type')->default('transactional'); // transactional, newsletter
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('trigger_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_sequences');
    }
};
