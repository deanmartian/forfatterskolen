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
        Schema::create('request_to_editors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_type');
            $table->unsignedInteger('editor_id');
            $table->unsignedInteger('manuscript_id');
            $table->date('answer_until');
            $table->string('answer')->nullable();
            $table->timestamps();

            $table->foreign('editor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_to_editors');
    }
};
