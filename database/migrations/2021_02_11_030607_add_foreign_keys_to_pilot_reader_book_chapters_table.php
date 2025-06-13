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
        Schema::table('pilot_reader_book_chapters', function (Blueprint $table) {
            $table->foreign('pilot_reader_book_id', 'pilot_reader_book_chapters_book_id')->references('id')->on('pilot_reader_books')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_book_chapters', function (Blueprint $table) {
            $table->dropForeign('pilot_reader_book_chapters_book_id');
        });
    }
};
