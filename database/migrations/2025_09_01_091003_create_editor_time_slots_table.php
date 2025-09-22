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
        Schema::create('editor_time_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('editor_id');
            $table->date('date');
            $table->time('start_time');
            $table->integer('duration')->default(60);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editor_time_slots');
    }
};
