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
        Schema::create('project_registration_paid_distribution_cost', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('project_registration_id');
            $table->string('years');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_registration_paid_distribution_cost');
    }
};
