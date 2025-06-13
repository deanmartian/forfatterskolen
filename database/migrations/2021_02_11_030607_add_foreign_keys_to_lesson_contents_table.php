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
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->foreign('lesson_id', 'lesson_contents_lesson_id')->references('id')->on('lessons')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->dropForeign('lesson_contents_lesson_id');
        });
    }
};
