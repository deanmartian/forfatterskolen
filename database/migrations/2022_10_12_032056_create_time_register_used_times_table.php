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
        Schema::create('time_register_used_times', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('time_register_id');
            $table->date('date');
            $table->decimal('time_used');
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->foreign('time_register_id')->references('id')->on('time_registers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_register_used_times');
    }
};
