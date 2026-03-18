<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_assignment_id');
            $table->unsignedInteger('user_id');
            $table->text('answer_text');
            $table->text('ai_feedback')->nullable();
            $table->text('approved_feedback')->nullable();
            $table->string('status', 20)->default('pending'); // pending, ai_generated, approved
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('lesson_assignment_id')->references('id')->on('lesson_assignments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['lesson_assignment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
