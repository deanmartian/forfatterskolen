<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('course_group_members');
    }
};
