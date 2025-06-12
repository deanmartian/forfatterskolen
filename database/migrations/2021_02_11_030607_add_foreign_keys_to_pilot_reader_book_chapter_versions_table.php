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
        Schema::table('pilot_reader_book_chapter_versions', function (Blueprint $table) {
            $table->foreign('chapter_id', 'pilot_reader_book_chapter_versions_chapter_id')->references('id')->on('pilot_reader_book_chapters')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_book_chapter_versions', function (Blueprint $table) {
            $table->dropForeign('pilot_reader_book_chapter_versions_chapter_id');
        });
    }
};
