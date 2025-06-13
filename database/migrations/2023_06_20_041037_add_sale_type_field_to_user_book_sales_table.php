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
        Schema::table('user_book_sales', function (Blueprint $table) {
            $table->enum('sale_type', ['physical', 'ebook', 'sound_book'])->default('physical')->after('user_book_for_sale_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_book_sales', function (Blueprint $table) {
            $table->dropColumn('sale_type');
        });
    }
};
