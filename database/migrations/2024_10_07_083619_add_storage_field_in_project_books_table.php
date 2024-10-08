<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStorageFieldInProjectBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_books', function (Blueprint $table) {
            $table->tinyInteger('in_storage')->after('isbn_ebook')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_books', function (Blueprint $table) {
            $table->dropColumn('in_storage');
        });
    }
}
