<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreparationFileToCoachingTimerTaken extends Migration
{
    public function up()
    {
        Schema::table('coaching_timer_manuscripts', function (Blueprint $table) {
            $table->string('preparation_file')->nullable();
            $table->text('preparation_notes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('coaching_timer_manuscripts', function (Blueprint $table) {
            $table->dropColumn(['preparation_file', 'preparation_notes']);
        });
    }
}
