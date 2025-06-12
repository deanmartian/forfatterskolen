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
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->foreign('coursetaken_id', 'manuscripts_ibfk_11')->references('id')->on('courses_taken')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('feedback_user_id', 'manuscripts_ibfk_12')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->dropForeign('manuscripts_ibfk_11');
            $table->dropForeign('manuscripts_ibfk_12');
        });
    }
};
