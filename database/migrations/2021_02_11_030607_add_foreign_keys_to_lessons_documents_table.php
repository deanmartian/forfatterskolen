<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLessonsDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons_documents', function (Blueprint $table) {
            $table->foreign('lesson_id', 'FK_lessons_documents_lessons')->references('id')->on('lessons')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons_documents', function (Blueprint $table) {
            $table->dropForeign('FK_lessons_documents_lessons');
        });
    }
}
