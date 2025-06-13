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
        Schema::table('assignment_group_learners', function (Blueprint $table) {
            $table->string('could_send_feedback_to')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_group_learners', function (Blueprint $table) {
            $table->dropColumn('could_send_feedback_to');
        });
    }
};
