<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAssignmentManuscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_manuscripts', function (Blueprint $table) {
            $table->foreign('assignment_id', 'assignment_manuscripts_ibfk_1')->references('id')->on('assignments')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'assignment_manuscripts_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
            $table->dropForeign('assignment_manuscripts_ibfk_1');
            $table->dropForeign('assignment_manuscripts_ibfk_2');
        });
    }
}
