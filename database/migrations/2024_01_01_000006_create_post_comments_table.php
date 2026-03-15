<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('post_comments')) {
            Schema::create('post_comments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('post_id');
                $table->unsignedInteger('user_id');
                $table->text('content');
                $table->timestamps();

                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('post_id');
            });
        } else {
            Schema::table('post_comments', function (Blueprint $table) {
                if (!Schema::hasColumn('post_comments', 'post_id')) {
                    $table->uuid('post_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('post_comments', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('post_id');
                }
                if (!Schema::hasColumn('post_comments', 'content')) {
                    $table->text('content')->nullable()->after('user_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
