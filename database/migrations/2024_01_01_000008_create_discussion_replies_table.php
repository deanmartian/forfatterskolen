<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('discussion_id');
            $table->unsignedInteger('user_id');
            $table->text('content');
            $table->timestamps();

            $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('discussion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
    }
};
