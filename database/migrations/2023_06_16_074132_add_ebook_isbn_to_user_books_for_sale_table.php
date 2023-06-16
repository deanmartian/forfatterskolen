<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEbookIsbnToUserBooksForSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_books_for_sale', function (Blueprint $table) {
            $table->string('ebook_isbn')->nullable()->after('isbn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_books_for_sale', function (Blueprint $table) {
            $table->dropColumn('ebook_isbn');
        });
    }
}
