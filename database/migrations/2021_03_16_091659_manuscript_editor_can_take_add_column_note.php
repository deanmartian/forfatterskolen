<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ManuscriptEditorCanTakeAddColumnNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manuscript_editor_can_takes', function (Blueprint $table) {
            $table->string('note', 1000)->after('how_many_hours')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manuscript_editor_can_takes', function ($table) {
            $table->dropColumn('note');
        });
    }
}
