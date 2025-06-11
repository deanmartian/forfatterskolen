<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPilotReaderBookInvitationLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pilot_reader_book_invitation_links', function (Blueprint $table) {
            $table->foreign('book_id', 'invitation_links_book_id_foreign')->references('id')->on('pilot_reader_books')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pilot_reader_book_invitation_links', function (Blueprint $table) {
            $table->dropForeign('invitation_links_book_id_foreign');
        });
    }
}
