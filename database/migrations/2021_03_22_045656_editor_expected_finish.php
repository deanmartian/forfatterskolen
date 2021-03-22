<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditorExpectedFinish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->date('editor_expected_finish')->after('parent')->nullable();
        });
        Schema::table('assignment_manuscripts', function ($table) {
            $table->date('editor_expected_finish')->after('expected_finish')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignments', function ($table) {
            $table->dropColumn('editor_expected_finish');
        });
        Schema::table('assignment_manuscripts', function ($table) {
            $table->dropColumn('editor_expected_finish');
        });
    }
}
