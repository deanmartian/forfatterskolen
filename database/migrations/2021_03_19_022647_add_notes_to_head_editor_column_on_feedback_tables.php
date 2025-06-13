<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignment_feedbacks', function (Blueprint $table) {
            $table->string('notes_to_head_editor', 1000)->after('hours_worked')->default('')->nullable();
        });
        Schema::table('assignment_feedbacks_no_group', function (Blueprint $table) {
            $table->string('notes_to_head_editor', 1000)->after('hours_worked')->default('')->nullable();
        });
        Schema::table('shop_manuscript_taken_feedbacks', function (Blueprint $table) {
            $table->string('notes_to_head_editor', 1000)->after('hours_worked')->default('')->nullable();
        });
        Schema::table('other_service_feedbacks', function (Blueprint $table) {
            $table->string('notes_to_head_editor', 1000)->after('hours_worked')->default('')->nullable();
        });
        Schema::table('coaching_timer_manuscripts', function (Blueprint $table) {
            $table->string('notes_to_head_editor', 1000)->after('hours_worked')->default('')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_feedbacks', function ($table) {
            $table->dropColumn('notes_to_head_editor');
        });
        Schema::table('assignment_feedbacks_no_group', function ($table) {
            $table->dropColumn('notes_to_head_editor');
        });
        Schema::table('shop_manuscript_taken_feedbacks', function ($table) {
            $table->dropColumn('notes_to_head_editor');
        });
        Schema::table('other_service_feedbacks', function ($table) {
            $table->dropColumn('notes_to_head_editor');
        });
        Schema::table('coaching_timer_manuscripts', function ($table) {
            $table->dropColumn('notes_to_head_editor');
        });
    }
};
