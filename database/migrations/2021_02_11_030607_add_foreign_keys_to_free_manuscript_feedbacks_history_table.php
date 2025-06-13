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
        Schema::table('free_manuscript_feedbacks_history', function (Blueprint $table) {
            $table->foreign('free_manuscript_id', 'Table: free_manuscript_feedbacks_history_free_manuscript_id')->references('id')->on('free_manuscripts')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('free_manuscript_feedbacks_history', function (Blueprint $table) {
            $table->dropForeign('Table: free_manuscript_feedbacks_history_free_manuscript_id');
        });
    }
};
