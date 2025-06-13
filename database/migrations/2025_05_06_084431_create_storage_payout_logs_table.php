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
        Schema::create('storage_payout_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_registration_id');
            $table->integer('year');
            $table->tinyInteger('quarter');
            $table->decimal('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_payout_logs');
    }
};
