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
        Schema::create('assignment_manuscript_editor_can_takes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('assignment_manuscript_id');
            $table->unsignedInteger('editor_id');
            $table->tinyInteger('how_many_you_can_take');
            $table->timestamps();

            $table->foreign('assignment_manuscript_id', 'assignment_manu_f')->references('id')->on('assignment_manuscripts');
            $table->foreign('editor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_manuscript_editor_can_takes');
    }
};
