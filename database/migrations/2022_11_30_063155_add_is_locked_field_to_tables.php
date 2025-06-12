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
        Schema::table('copy_editing_manuscripts', function (Blueprint $table) {
            $table->tinyInteger('is_locked')->default(0)->after('expected_finish');
        });

        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->tinyInteger('is_locked')->default(0)->after('expected_finish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('copy_editing_manuscripts', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });

        Schema::table('correction_manuscripts', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
