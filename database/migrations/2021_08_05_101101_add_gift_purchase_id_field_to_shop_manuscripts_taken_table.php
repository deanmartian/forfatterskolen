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
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->unsignedInteger('gift_purchase_id')->nullable()->after('package_shop_manuscripts_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_manuscripts_taken', function (Blueprint $table) {
            $table->dropColumn('gift_purchase_id');
        });
    }
};
