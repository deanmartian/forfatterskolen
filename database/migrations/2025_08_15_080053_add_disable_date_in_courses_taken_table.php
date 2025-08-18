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
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->date('disable_start_date')->nullable()->after('end_date');
            $table->date('disable_end_date')->nullable()->after('disable_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->dropColumn('disable_start_date');
            $table->dropColumn('disable_end_date');
        });
    }
};
