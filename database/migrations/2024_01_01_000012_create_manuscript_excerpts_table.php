<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manuscript_excerpts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('content');
            $table->integer('word_count')->default(0);
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('manuscript_projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscript_excerpts');
    }
};
