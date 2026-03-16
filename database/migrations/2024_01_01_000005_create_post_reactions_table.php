<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('post_reactions')) {
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
        } else {
            Schema::table('post_reactions', function (Blueprint $table) {
                if (!Schema::hasColumn('post_reactions', 'post_id')) {
                    $table->uuid('post_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('post_reactions', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('post_id');
                }
                if (!Schema::hasColumn('post_reactions', 'reaction')) {
                    $table->string('reaction')->default('like')->after('user_id');
                }
                if (!Schema::hasColumn('post_reactions', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
