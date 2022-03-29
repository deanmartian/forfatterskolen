<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanReceiveEmailFieldToCoursesTakenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->tinyInteger('can_receive_email')->after('is_welcome_email_sent')->default(1);
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
            $table->dropColumn('can_receive_email');
        });
    }
}
