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
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('months_3_price')->nullable()->change();
            $table->decimal('months_6_price')->nullable()->change();
            $table->decimal('months_12_price')->nullable()->change();
            $table->string('months_3_product')->nullable()->change();
            $table->string('months_6_product')->nullable()->change();
            $table->string('months_12_product')->nullable()->change();
            $table->integer('months_3_due_date')->nullable()->change();
            $table->integer('months_6_due_date')->nullable()->change();
            $table->integer('months_12_due_date')->nullable()->change();
            $table->smallInteger('months_3_enable')->default(0)->change();
            $table->smallInteger('months_6_enable')->default(0)->change();
            $table->smallInteger('months_12_enable')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('months_3_price')->change();
            $table->decimal('months_6_price')->change();
            $table->decimal('months_12_price')->change();
            $table->string('months_3_product')->change();
            $table->string('months_6_product')->change();
            $table->string('months_12_product')->change();
            $table->integer('months_3_due_date')->change();
            $table->integer('months_6_due_date')->change();
            $table->integer('months_12_due_date')->change();
            $table->smallInteger('months_3_enable')->default(1)->change();
            $table->smallInteger('months_6_enable')->default(1)->change();
            $table->smallInteger('months_12_enable')->default(1)->change();
        });
    }
};
