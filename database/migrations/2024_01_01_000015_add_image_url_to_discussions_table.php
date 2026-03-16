<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('discussions') && !Schema::hasColumn('discussions', 'image_url')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('content');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('discussions', 'image_url')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }
    }
};
