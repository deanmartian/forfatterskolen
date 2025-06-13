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
        Schema::table('pilot_reader_book_chapter_notes', function (Blueprint $table) {
            $table->foreign('pilot_reader_book_chapter_id', 'pilot_reader_book_chapter_id')->references('id')->on('pilot_reader_book_chapters')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_book_chapter_notes', function (Blueprint $table) {
            $table->dropForeign('pilot_reader_book_chapter_id');
        });
    }
};
