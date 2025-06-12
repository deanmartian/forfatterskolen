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
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->timestamp('deadline')->nullable()->after('feedback_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->removeColumn('deadline');
        });
    }
};
