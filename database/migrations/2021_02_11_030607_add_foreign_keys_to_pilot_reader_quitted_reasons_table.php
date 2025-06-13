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
        Schema::table('pilot_reader_quitted_reasons', function (Blueprint $table) {
            $table->foreign('book_reader_id', 'book_reader_id_foreign')->references('id')->on('pilot_reader_book_reading')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_quitted_reasons', function (Blueprint $table) {
            $table->dropForeign('book_reader_id_foreign');
        });
    }
};
