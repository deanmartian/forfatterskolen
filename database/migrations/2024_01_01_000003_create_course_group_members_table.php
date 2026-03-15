<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_group_members')) {
            Schema::create('course_group_members', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('course_group_id');
                $table->unsignedInteger('user_id');
                $table->string('role')->default('member');
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('course_group_id')->references('id')->on('course_groups')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['course_group_id', 'user_id']);
            });
        } else {
            Schema::table('course_group_members', function (Blueprint $table) {
                if (!Schema::hasColumn('course_group_members', 'course_group_id')) {
                    $table->uuid('course_group_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('course_group_members', 'user_id')) {
                    $table->unsignedInteger('user_id')->nullable()->after('course_group_id');
                }
                if (!Schema::hasColumn('course_group_members', 'role')) {
                    $table->string('role')->default('member')->after('user_id');
                }
                if (!Schema::hasColumn('course_group_members', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_group_members');
    }
};
