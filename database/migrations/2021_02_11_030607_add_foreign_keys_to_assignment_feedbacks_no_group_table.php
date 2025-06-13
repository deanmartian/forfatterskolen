<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignment_feedbacks_no_group', function (Blueprint $table) {
            $table->foreign('assignment_manuscript_id', 'assignment_feedbacks_no_group_assignment_id_ibfk_1')->references('id')->on('assignment_manuscripts')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('learner_id', 'assignment_feedbacks_no_group_learner_id_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('feedback_user_id', 'assignment_feedbacks_no_group_user_id_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_feedbacks_no_group', function (Blueprint $table) {
            $table->dropForeign('assignment_feedbacks_no_group_assignment_id_ibfk_1');
            $table->dropForeign('assignment_feedbacks_no_group_learner_id_ibfk_1');
            $table->dropForeign('assignment_feedbacks_no_group_user_id_ibfk_1');
        });
    }
};
