<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRenewedAtFieldInCoursesTakenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->timestamp('renewed_at')->nullable()->after('can_receive_email');
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
            $table->removeColumn('renewed_at');
        });
    }
}
