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
        Schema::table('pilot_reader_book_invitation_links', function (Blueprint $table) {
            $table->foreign('book_id', 'invitation_links_book_id_foreign')->references('id')->on('pilot_reader_books')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_book_invitation_links', function (Blueprint $table) {
            $table->dropForeign('invitation_links_book_id_foreign');
        });
    }
};
