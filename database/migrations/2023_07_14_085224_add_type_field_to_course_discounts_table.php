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
        Schema::table('course_discounts', function (Blueprint $table) {
            $table->tinyInteger('type')->default(1)->after('valid_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_discounts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
