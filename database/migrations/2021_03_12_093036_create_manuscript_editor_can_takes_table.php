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
        Schema::create('manuscript_editor_can_takes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('editor_id')->unsigned()->index('editor_id');
            $table->date('date_from');
            $table->date('date_to');
            $table->decimal('how_many_script', 11)->default(0);
            $table->decimal('how_many_hours', 11)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuscript_editor_can_takes');
    }
};
