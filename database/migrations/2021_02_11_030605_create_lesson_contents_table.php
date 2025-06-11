<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLessonContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_contents', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('lesson_id')->unsigned()->index('lesson_contents_lesson_id');
            $table->string('title');
            $table->text('lesson_content')->nullable();
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
        Schema::drop('lesson_contents');
    }
}
