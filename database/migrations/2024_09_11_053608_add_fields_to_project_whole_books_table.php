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
        Schema::table('project_whole_books', function (Blueprint $table) {
            $table->unsignedInteger('designer_id')->nullable()->after('is_file');
            $table->integer('page_count')->nullable()->after('designer_id');
            $table->integer('width')->nullable()->after('page_count');
            $table->integer('height')->nullable()->after('width');
            $table->longText('designer_description')->nullable()->after('height');
            $table->enum('status', ['pending', 'completed'])->after('designer_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_whole_books', function (Blueprint $table) {
            $table->dropColumn('designer_id');
            $table->dropColumn('page_count');
            $table->dropColumn('width');
            $table->dropColumn('height');
            $table->dropColumn('designer_description');
            $table->dropColumn('status');
        });
    }
};
