<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shop_manuscripts_upgrade', function (Blueprint $table) {
            $table->foreign('shop_manuscript_id', 'shop_manuscript_id_ibfk_1')->references('id')->on('shop_manuscripts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('upgrade_shop_manuscript_id', 'upgrade_shop_manuscript_id_ibfk_1')->references('id')->on('shop_manuscripts')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_manuscripts_upgrade', function (Blueprint $table) {
            $table->dropForeign('shop_manuscript_id_ibfk_1');
            $table->dropForeign('upgrade_shop_manuscript_id_ibfk_1');
        });
    }
};
