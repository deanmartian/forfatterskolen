<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_shop_manuscripts', function (Blueprint $table) {
            $table->text('coaching_topic')->nullable()->after('coaching_time_later');
        });
    }

    public function down(): void
    {
        Schema::table('order_shop_manuscripts', function (Blueprint $table) {
            $table->dropColumn('coaching_topic');
        });
    }
};
