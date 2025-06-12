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
        Schema::table('user_books_for_sale', function (Blueprint $table) {
            $table->string('isbn')->nullable()->after('user_id');
        });

        Schema::table('user_book_sales', function (Blueprint $table) {
            $table->decimal('amount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_books_for_sale', function (Blueprint $table) {
            $table->dropColumn('isbn');
        });

        Schema::table('user_book_sales', function (Blueprint $table) {
            $table->decimal('amount')->change();
        });
    }
};
