<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditorTimeSlotIdToCoachingTimerManuscriptsTable extends Migration
{
    public function up()
    {
        Schema::table('coaching_timer_manuscripts', function (Blueprint $table) {
            $table->unsignedBigInteger('editor_time_slot_id')->nullable()->after('editor_id');
            $table->foreign('editor_time_slot_id')
                ->references('id')->on('editor_time_slots')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('coaching_timer_manuscripts', function (Blueprint $table) {
            $table->dropForeign(['editor_time_slot_id']);
            $table->dropColumn('editor_time_slot_id');
        });
    }
}
