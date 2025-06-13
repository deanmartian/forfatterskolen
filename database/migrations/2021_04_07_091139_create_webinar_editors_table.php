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
        Schema::create('webinar_editors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('editor_id');
            $table->unsignedInteger('webinar_id');
            $table->string('presenter_url', 1000)->nullable();
            $table->timestamps();

            $table->foreign('editor_id')->references('id')->on('users');
            $table->foreign('webinar_id')->references('id')->on('webinars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webinar_editors');
    }
};
