<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->boolean('available_for_editors')->default(0)->after('is_manuscript_locked');
        });
    }

    public function down(): void
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->dropColumn('available_for_editors');
        });
    }
};
