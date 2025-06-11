<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSimilarCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('similar_courses', function (Blueprint $table) {
            $table->foreign('course_id', 'similar_courses_ibfk_1')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('similar_course_id', 'similar_courses_ibfk_2')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('similar_courses', function (Blueprint $table) {
            $table->dropForeign('similar_courses_ibfk_1');
            $table->dropForeign('similar_courses_ibfk_2');
        });
    }
}
