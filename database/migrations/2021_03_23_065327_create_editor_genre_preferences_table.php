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
        Schema::create('editor_genre_preferences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('editor_id');
            $table->unsignedInteger('genre_id');
            $table->timestamps();

            $table->foreign('editor_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('genre_id')->references('id')->on('genre')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editor_genre_preferences');
    }
};
