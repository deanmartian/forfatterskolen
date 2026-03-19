<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webinar_replay_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('webinar_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamp('sent_at')->useCurrent();

            $table->unique(['webinar_id', 'user_id']);

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_replay_notifications');
    }
};
