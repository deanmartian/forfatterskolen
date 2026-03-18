<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('type', 20)->default('module')->after('course_id');
            // Types: 'module' (ekte modul), 'resource' (kursplan, leseliste etc.), 'reprise' (repriser/opptak)
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
