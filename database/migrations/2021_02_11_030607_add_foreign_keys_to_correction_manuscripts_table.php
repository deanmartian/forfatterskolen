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
        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->foreign('editor_id', 'correction_manuscripts_editor')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->dropForeign('correction_manuscripts_editor');
        });
    }
};
