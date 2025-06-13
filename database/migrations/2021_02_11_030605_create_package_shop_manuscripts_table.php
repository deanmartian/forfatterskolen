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
        Schema::create('package_shop_manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned()->index('package_id');
            $table->integer('shop_manuscript_id')->unsigned()->index('shop_manuscript_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('package_shop_manuscripts');
    }
};
