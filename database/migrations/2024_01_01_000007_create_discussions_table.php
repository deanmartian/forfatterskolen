<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('discussions')) {
            Schema::create('discussions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedInteger('user_id');
                $table->string('title');
                $table->text('content');
                $table->string('category');
                $table->boolean('pinned')->default(false);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('category');
                $table->index('created_at');
            });
        } else {
            Schema::table('discussions', function (Blueprint $table) {
                if (!Schema::hasColumn('discussions', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('discussions', 'title')) {
                    $table->string('title')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('discussions', 'content')) {
                    $table->text('content')->nullable()->after('title');
                }
                if (!Schema::hasColumn('discussions', 'category')) {
                    $table->string('category')->nullable()->after('content');
                }
                if (!Schema::hasColumn('discussions', 'pinned')) {
                    $table->boolean('pinned')->default(false)->after('category');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};
