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
            $table->integer('coaching_timer_manuscript_id');
            // Matches editor_time_slots.id (BIGINT UNSIGNED)
            $table->foreignId('editor_time_slot_id')
                ->constrained('editor_time_slots')
                ->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('coaching_timer_manuscript_id')
                ->references('id')->on('coaching_timer_manuscripts')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('coaching_time_requests');
    }
}
