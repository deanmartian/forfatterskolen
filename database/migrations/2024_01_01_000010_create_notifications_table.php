<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('user_id');
            $table->string('type');
            $table->text('content');
            $table->unsignedInteger('from_user_id')->nullable();
            $table->boolean('read')->default(false);
            $table->string('link')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_notifications');
    }
};
