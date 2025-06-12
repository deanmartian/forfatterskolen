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
        Schema::table('solution_articles', function (Blueprint $table) {
            $table->foreign('solution_id', 'solution_id')->references('id')->on('solutions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solution_articles', function (Blueprint $table) {
            $table->dropForeign('solution_id');
        });
    }
};
