<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_group_shared_books', function (Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('pilot_reader_books')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('private_group_id')->references('id')->on('private_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_group_shared_books', function (Blueprint $table) {
            $table->dropForeign('private_group_shared_books_book_id_foreign');
            $table->dropForeign('private_group_shared_books_private_group_id_foreign');
        });
    }
};
