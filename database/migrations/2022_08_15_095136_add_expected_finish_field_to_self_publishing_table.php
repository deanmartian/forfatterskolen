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
        Schema::table('self_publishing', function (Blueprint $table) {
            $table->date('expected_finish')->after('editor_share')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('self_publishing', function (Blueprint $table) {
            $table->dropColumn('expected_finish');
        });
    }
};
