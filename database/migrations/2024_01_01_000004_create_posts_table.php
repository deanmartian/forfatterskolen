<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedInteger('user_id');
                $table->text('content');
                $table->string('image_url')->nullable();
                $table->boolean('pinned')->default(false);
                $table->unsignedInteger('course_group_id')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('course_group_id')->references('id')->on('courses')->onDelete('set null');
                $table->index('user_id');
                $table->index('created_at');
                $table->index('course_group_id');
            });
        } else {
            Schema::table('posts', function (Blueprint $table) {
                if (!Schema::hasColumn('posts', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('posts', 'content')) {
                    $table->text('content')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('posts', 'image_url')) {
                    $table->string('image_url')->nullable()->after('content');
                }
                if (!Schema::hasColumn('posts', 'pinned')) {
                    $table->boolean('pinned')->default(false)->after('image_url');
                }
                if (!Schema::hasColumn('posts', 'course_group_id')) {
                    $table->unsignedInteger('course_group_id')->nullable()->after('pinned');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
