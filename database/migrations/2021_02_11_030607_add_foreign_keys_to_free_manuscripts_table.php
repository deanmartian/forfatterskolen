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
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->foreign('editor_id', 'free_manuscripts_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->dropForeign('free_manuscripts_ibfk_1');
        });
    }
};
