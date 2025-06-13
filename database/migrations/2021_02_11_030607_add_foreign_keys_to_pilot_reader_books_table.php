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
        Schema::table('pilot_reader_books', function (Blueprint $table) {
            $table->foreign('user_id', 'pilot_reader_books_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_books', function (Blueprint $table) {
            $table->dropForeign('pilot_reader_books_user_id');
        });
    }
};
