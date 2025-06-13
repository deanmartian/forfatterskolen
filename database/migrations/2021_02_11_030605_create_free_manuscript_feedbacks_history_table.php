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
        Schema::create('free_manuscript_feedbacks_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('free_manuscript_id')->unsigned()->index('free_manuscript_id');
            $table->timestamp('date_sent')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('free_manuscript_feedbacks_history');
    }
};
