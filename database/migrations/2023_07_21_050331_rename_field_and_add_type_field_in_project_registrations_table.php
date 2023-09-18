<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFieldAndAddTypeFieldInProjectRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_registrations', function (Blueprint $table) {
            $table->renameColumn('type', 'field');
        });

        Schema::table('project_registrations', function (Blueprint $table) {
            $table->tinyInteger('type')->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_registrations', function (Blueprint $table) {
            $table->renameColumn('field', 'type');
            $table->dropColumn('type');
        });
    }
}
