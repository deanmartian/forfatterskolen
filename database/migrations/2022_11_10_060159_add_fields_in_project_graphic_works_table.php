<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInProjectGraphicWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_graphic_works', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('value');
            $table->date('date')->nullable()->after('description');
            $table->tinyInteger('is_checked')->default(0)->after('date');
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
            $table->dropColumn('date');
            $table->dropColumn('is_checked');
        });
    }
}
