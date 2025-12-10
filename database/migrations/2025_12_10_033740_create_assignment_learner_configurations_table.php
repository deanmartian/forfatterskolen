<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignment_learner_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('assignment_id');
            $table->unsignedInteger('user_id');
            $table->integer('max_words')->nullable();
            $table->timestamps();
            
            $table->index(['assignment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_learner_configurations');
    }
};
