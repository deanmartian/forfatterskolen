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
        Schema::table('course_applications', function (Blueprint $table) {
            $table->dropColumn('optional_words');
            $table->dropColumn('reason_for_applying');
            $table->dropColumn('need_in_course');
            $table->dropColumn('expectations');
            $table->dropColumn('how_ready');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_applications', function (Blueprint $table) {
            $table->text('optional_words')->nullable();
            $table->longText('reason_for_applying');
            $table->longText('need_in_course');
            $table->longText('expectations');
            $table->longText('how_ready');
        });
    }
};
