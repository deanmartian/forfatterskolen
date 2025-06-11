<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFreeManuscriptFeedbacksHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_manuscript_feedbacks_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('free_manuscript_id')->unsigned()->index('free_manuscript_id');
            $table->timestamp('date_sent')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::drop('free_manuscript_feedbacks_history');
    }
}
