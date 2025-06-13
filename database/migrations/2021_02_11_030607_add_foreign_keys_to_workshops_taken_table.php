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
        Schema::table('workshops_taken', function (Blueprint $table) {
            $table->foreign('user_id', 'workshops_taken_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('workshop_id', 'workshops_taken_ibfk_2')->references('id')->on('workshops')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('menu_id', 'workshops_taken_ibfk_3')->references('id')->on('workshop_menus')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops_taken', function (Blueprint $table) {
            $table->dropForeign('workshops_taken_ibfk_1');
            $table->dropForeign('workshops_taken_ibfk_2');
            $table->dropForeign('workshops_taken_ibfk_3');
        });
    }
};
