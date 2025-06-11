<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoursesSharedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses_shared', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->integer('package_id')->unsigned()->index('package_id');
            $table->string('hash', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('courses_shared');
    }
}
