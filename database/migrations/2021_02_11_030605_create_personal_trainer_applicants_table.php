<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePersonalTrainerApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_trainer_applicants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->unsigned()->index('pt_applicants_user_id_foreign');
            $table->integer('age')->nullable();
            $table->text('optional_words', 65535)->nullable();
            $table->text('reason_for_applying', 65535);
            $table->text('need_in_course', 65535);
            $table->text('expectations', 65535);
            $table->text('how_ready', 65535);
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
        Schema::drop('personal_trainer_applicants');
    }
}
