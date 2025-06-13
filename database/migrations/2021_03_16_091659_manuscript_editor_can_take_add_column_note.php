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
        Schema::table('manuscript_editor_can_takes', function (Blueprint $table) {
            $table->string('note', 1000)->after('how_many_hours')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manuscript_editor_can_takes', function ($table) {
            $table->dropColumn('note');
        });
    }
};
