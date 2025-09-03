<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoachingTimeRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('coaching_time_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coaching_timer_manuscript_id');
            $table->unsignedBigInteger('editor_time_slot_id');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('coaching_timer_manuscript_id')
                ->references('id')->on('coaching_timer_manuscripts')
                ->onDelete('cascade');
            $table->foreign('editor_time_slot_id')
                ->references('id')->on('editor_time_slots')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('coaching_time_requests');
    }
}
