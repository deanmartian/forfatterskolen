<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->index('FK_course_discounts_courses');
            $table->string('coupon', 100);
            $table->integer('discount');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
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
        Schema::drop('course_discounts');
    }
}
