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
        Schema::table('pilot_reader_book_chapter_feedback_messages', function (Blueprint $table) {
            $table->foreign('feedback_id', 'feedback_id')->references('id')->on('pilot_reader_book_chapter_feedback')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_book_chapter_feedback_messages', function (Blueprint $table) {
            $table->dropForeign('feedback_id');
        });
    }
};
