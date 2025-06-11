<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPilotReaderBookInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pilot_reader_book_invitation', function (Blueprint $table) {
            $table->foreign('book_id', 'book_id')->references('id')->on('pilot_reader_books')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pilot_reader_book_invitation', function (Blueprint $table) {
            $table->dropForeign('book_id');
        });
    }
}
