<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLetterToEditorInAssignmentManuscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_manuscripts', function (Blueprint $table) {
            $table->string('letter_to_editor')->nullable()->after('join_group');
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
            $table->string('letter_to_editor');
        });
    }
}
