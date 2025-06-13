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
        Schema::table('project_graphic_works', function (Blueprint $table) {
            $table->date('upload_date')->nullable()->after('is_checked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_graphic_works', function (Blueprint $table) {
            $table->dropColumn('upload_date');
        });
    }
};
