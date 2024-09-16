<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProjectGraphicWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_graphic_works', function (Blueprint $table) {
            $table->unsignedInteger('isbn_id')->nullable()->after('format');
            $table->string('backside_text')->nullable()->after('isbn_id');
            $table->string('backside_image')->nullable()->after('backside_text');
            $table->string('instruction')->nullable()->after('backside_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_graphic_works', function (Blueprint $table) {
            $table->dropColumn('isbn_id');
            $table->dropColumn('backside_text');
            $table->dropColumn('backside_image');
            $table->dropColumn('instruction');
        });
    }
}
