<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storage_details', function (Blueprint $table) {
            $table->dropForeign(['user_book_for_sale_id']);
            $table->dropColumn('user_book_for_sale_id');
            $table->unsignedInteger('project_book_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storage_details', function (Blueprint $table) {
            $table->unsignedInteger('user_book_for_sale_id');
            $table->dropColumn('project_book_id');
        });
    }
};
