<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'show_in_course_groups')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->boolean('show_in_course_groups')->default(true)->after('hide_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'show_in_course_groups')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('show_in_course_groups');
            });
        }
    }
};
