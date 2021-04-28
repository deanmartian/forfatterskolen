<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusOnAssignmentManuscriptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_manuscripts', function (Blueprint $table) {
            $table->string('manuscript_status', 50)->after('editor_expected_finish')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_manuscripts', function (Blueprint $table) {
            $table->dropColumn('manuscript_status');
        });
    }
}
