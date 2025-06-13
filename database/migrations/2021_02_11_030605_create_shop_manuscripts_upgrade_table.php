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
        Schema::create('shop_manuscripts_upgrade', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('shop_manuscript_id')->unsigned()->index('shop_manuscript_id');
            $table->integer('upgrade_shop_manuscript_id')->unsigned()->index('upgrade_shop_manuscript_id');
            $table->decimal('price', 11);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('shop_manuscripts_upgrade');
    }
};
