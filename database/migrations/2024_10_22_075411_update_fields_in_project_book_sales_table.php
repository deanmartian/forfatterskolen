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
        Schema::table('project_book_sales', function (Blueprint $table) {
            $table->dropColumn('sale_type');
            $table->string('customer_name')->nullable()->after('project_book_id');
            $table->decimal('full_price')->nullable()->after('quantity');
            $table->decimal('discount')->nullable()->after('full_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_book_sales', function (Blueprint $table) {
            $table->string('sale_type')->after('project_book_id');
            $table->dropColumn('customer_name');
            $table->dropColumn('full_price');
            $table->dropColumn('discount');
        });
    }
};
