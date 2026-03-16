<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_shop_manuscripts', function (Blueprint $table) {
            $table->string('coaching_file')->nullable()->after('coaching_topic');
            $table->string('coaching_file_name')->nullable()->after('coaching_file');
        });
    }

    public function down(): void
    {
        Schema::table('order_shop_manuscripts', function (Blueprint $table) {
            $table->dropColumn(['coaching_file', 'coaching_file_name']);
        });
    }
};
