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
        Schema::create('custom_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('link')->nullable();
            $table->dateTime('last_run')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('custom_actions');
    }
};
