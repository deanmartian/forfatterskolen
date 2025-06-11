<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFreeManuscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('email', 100)->default('');
            $table->text('content');
            $table->integer('genre')->default(0);
            $table->integer('editor_id')->unsigned()->nullable()->index('editor_id');
            $table->integer('is_feedback_sent')->default(0);
            $table->text('feedback_content', 65535)->nullable();
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
        Schema::drop('free_manuscripts');
    }
}
