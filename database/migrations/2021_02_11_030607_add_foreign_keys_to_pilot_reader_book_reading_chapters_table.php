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
        Schema::table('pilot_reader_book_reading_chapters', function (Blueprint $table) {
            $table->foreign('user_id', 'pilot_reader_book_reading_chapters_chapter_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_book_reading_chapters', function (Blueprint $table) {
            $table->dropForeign('pilot_reader_book_reading_chapters_chapter_id');
        });
    }
};
