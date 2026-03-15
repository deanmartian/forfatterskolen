<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manuscript_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('excerpt_id');
            $table->unsignedInteger('user_id');
            $table->text('content');
            $table->timestamps();

            $table->foreign('excerpt_id')->references('id')->on('manuscript_excerpts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('excerpt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscript_feedback');
    }
};
