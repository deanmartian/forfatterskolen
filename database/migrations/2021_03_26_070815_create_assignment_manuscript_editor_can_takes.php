<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentManuscriptEditorCanTakes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_manuscript_editor_can_takes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('assignment_manuscript_id');
            $table->unsignedInteger('editor_id');
            $table->tinyInteger('how_many_you_can_take');
            $table->timestamps();

            $table->foreign('assignment_manuscript_id', 'assignment_manu_f')->references('id')->on('assignment_manuscripts');
            $table->foreign('editor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignment_manuscript_editor_can_takes');
    }
}
