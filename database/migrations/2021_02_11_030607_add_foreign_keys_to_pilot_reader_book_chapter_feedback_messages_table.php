<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPilotReaderBookChapterFeedbackMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pilot_reader_book_chapter_feedback_messages', function (Blueprint $table) {
            $table->foreign('feedback_id', 'feedback_id')->references('id')->on('pilot_reader_book_chapter_feedback')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pilot_reader_book_chapter_feedback_messages', function (Blueprint $table) {
            $table->dropForeign('feedback_id');
        });
    }
}
