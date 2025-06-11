<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWordsWrittenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words_written', function (Blueprint $table) {
            $table->foreign('user_id', 'words_written_user_id_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words_written', function (Blueprint $table) {
            $table->dropForeign('words_written_user_id_ibfk_1');
        });
    }
}
