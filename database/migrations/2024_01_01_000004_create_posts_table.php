<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
