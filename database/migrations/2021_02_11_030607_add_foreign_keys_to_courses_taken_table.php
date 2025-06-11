<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCoursesTakenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->foreign('user_id', 'courses_taken_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('package_id', 'courses_taken_ibfk_2')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->dropForeign('courses_taken_ibfk_1');
            $table->dropForeign('courses_taken_ibfk_2');
        });
    }
}
