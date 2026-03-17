<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('anthology_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('connection'); // elev | tidligere_elev | ny
            $table->string('course_name')->nullable();
            $table->string('title');
            $table->string('genre'); // novelle, krim, barnefortelling, dikt, feelgood, sakprosa
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('word_count')->nullable();
            $table->boolean('consent_terms')->default(true);
            $table->boolean('consent_marketing')->default(false);
            $table->string('status')->default('received'); // received | under_review | selected | not_selected | feedback_sent
            $table->text('editor_feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('anthology_submissions');
    }
};
