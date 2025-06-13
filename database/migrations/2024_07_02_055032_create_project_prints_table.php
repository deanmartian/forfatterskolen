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
        Schema::create('project_prints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id');
            $table->string('isbn');
            $table->integer('number');
            $table->integer('pages');
            $table->string('format');
            $table->integer('width');
            $table->integer('height');
            $table->string('originals');
            $table->string('binding');
            $table->string('yarn_stapling');
            $table->string('media');
            $table->string('print_method');
            $table->string('color');
            $table->integer('number_of_color_pages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_prints');
    }
};
