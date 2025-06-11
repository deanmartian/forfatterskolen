<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPilotReaderBookChapterVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pilot_reader_book_chapter_versions', function (Blueprint $table) {
            $table->foreign('chapter_id', 'pilot_reader_book_chapter_versions_chapter_id')->references('id')->on('pilot_reader_book_chapters')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pilot_reader_book_chapter_versions', function (Blueprint $table) {
            $table->dropForeign('pilot_reader_book_chapter_versions_chapter_id');
        });
    }
}
