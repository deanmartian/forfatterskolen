<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('other_service_feedbacks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('service_id');
            $table->integer('service_type');
            $table->string('manuscript');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('other_service_feedbacks');
    }
};
