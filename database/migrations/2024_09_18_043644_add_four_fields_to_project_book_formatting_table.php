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
        Schema::table('project_book_formatting', function (Blueprint $table) {
            $table->string('corporate_page')->nullable()->after('file');
            $table->string('format')->nullable()->after('corporate_page');
            $table->string('format_image')->nullable()->after('format');
            $table->longText('description')->nullable()->after('format_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_book_formatting', function (Blueprint $table) {
            $table->dropColumn('corporate_page');
            $table->dropColumn('format');
            $table->dropColumn('format_image');
            $table->dropColumn('description');
        });
    }
};
