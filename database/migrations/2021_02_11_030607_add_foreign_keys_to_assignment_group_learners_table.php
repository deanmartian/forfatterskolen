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
        Schema::table('assignment_group_learners', function (Blueprint $table) {
            $table->foreign('user_id', 'assignment_group_learners_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('assignment_group_id', 'assignment_group_learners_ibfk_3')->references('id')->on('assignment_groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_group_learners', function (Blueprint $table) {
            $table->dropForeign('assignment_group_learners_ibfk_2');
            $table->dropForeign('assignment_group_learners_ibfk_3');
        });
    }
};
