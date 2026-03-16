<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('profiles') && !Schema::hasColumn('profiles', 'use_author_name')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->boolean('use_author_name')->default(false)->after('author_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('profiles') && Schema::hasColumn('profiles', 'use_author_name')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('use_author_name');
            });
        }
    }
};
