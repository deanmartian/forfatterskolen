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
        Schema::table('workshop_taken_count', function (Blueprint $table) {
            $table->foreign('user_id', 'workshop_taken_count_user_id_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_taken_count', function (Blueprint $table) {
            $table->dropForeign('workshop_taken_count_user_id_ibfk_1');
        });
    }
};
