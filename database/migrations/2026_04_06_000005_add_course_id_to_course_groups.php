<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('course_groups', 'course_id')) {
                $table->unsignedInteger('course_id')->nullable()->after('icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_groups', function (Blueprint $table) {
            $table->dropColumn('course_id');
        });
    }
};
