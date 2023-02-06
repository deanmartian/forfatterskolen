<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsOnBookForSaleAndSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_books_for_sale', function (Blueprint $table) {
            $table->string('isbn')->nullable()->after('user_id');
        });

        Schema::table('user_book_sales', function (Blueprint $table) {
            $table->decimal('amount')->nullable()->change();
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
            $table->dropColumn('isbn');
        });

        Schema::table('user_book_sales', function (Blueprint $table) {
            $table->decimal('amount')->change();
        });
    }
}
