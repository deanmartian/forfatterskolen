<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFreeManuscriptFeedbacksHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('free_manuscript_feedbacks_history', function (Blueprint $table) {
            $table->foreign('free_manuscript_id', 'Table: free_manuscript_feedbacks_history_free_manuscript_id')->references('id')->on('free_manuscripts')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_manuscript_feedbacks_history', function (Blueprint $table) {
            $table->dropForeign('Table: free_manuscript_feedbacks_history_free_manuscript_id');
        });
    }
}
