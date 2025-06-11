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
        Schema::table('project_book_sales', function (Blueprint $table) {
            $table->integer('project_registration_id')->nullable()->after('project_book_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_book_sales', function (Blueprint $table) {
            $table->dropColumn('project_registration_id');
        });
    }
};
