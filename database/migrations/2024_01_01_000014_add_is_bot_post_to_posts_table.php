<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts') && !Schema::hasColumn('posts', 'is_bot_post')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->boolean('is_bot_post')->default(false)->after('pinned');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'is_bot_post')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('is_bot_post');
            });
        }
    }
};
