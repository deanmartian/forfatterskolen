<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id');
            $table->unsignedInteger('user_id');
            $table->string('reaction')->default('like');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['post_id', 'user_id', 'reaction']);
            $table->index('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
