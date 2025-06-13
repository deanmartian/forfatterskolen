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
        Schema::create('marketing_plan_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('marketing_plan_id');
            $table->longText('main_question')->nullable();
            $table->longText('sub_question')->nullable();
            $table->timestamps();

            $table->foreign('marketing_plan_id')->references('id')->on('marketing_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_plan_questions');
    }
};
