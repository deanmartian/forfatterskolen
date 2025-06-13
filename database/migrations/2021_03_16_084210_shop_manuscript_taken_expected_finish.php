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
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->date('editor_expected_finish')->after('expected_finish')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_manuscripts_taken', function ($table) {
            $table->dropColumn('editor_expected_finish');
        });
    }
};
