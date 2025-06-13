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
        Schema::table('pilot_reader_book_invitation', function (Blueprint $table) {
            $table->foreign('book_id', 'book_id')->references('id')->on('pilot_reader_books')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_book_invitation', function (Blueprint $table) {
            $table->dropForeign('book_id');
        });
    }
};
