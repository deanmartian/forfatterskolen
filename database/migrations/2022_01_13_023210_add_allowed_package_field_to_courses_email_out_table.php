<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllowedPackageFieldToCoursesEmailOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->string('allowed_package')->nullable()->after('from_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses_email_out', function (Blueprint $table) {
            $table->dropColumn('allowed_package');
        });
    }
}
