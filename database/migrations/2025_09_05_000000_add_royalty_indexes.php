<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_book_sales', function (Blueprint $table) {
            $table->index(['project_book_id', 'date'], 'project_book_sales_book_date_index');
            $table->index('date', 'project_book_sales_date_index');
        });

        Schema::table('storage_distribution_costs', function (Blueprint $table) {
            $table->index(['project_book_id', 'date'], 'storage_distribution_costs_book_date_index');
            $table->index('date', 'storage_distribution_costs_date_index');
        });

        Schema::table('storage_payouts', function (Blueprint $table) {
            $table->index(['project_registration_id', 'year', 'quarter'], 'storage_payouts_registration_year_quarter_index');
            $table->index(['year', 'quarter'], 'storage_payouts_year_quarter_index');
        });
    }

    public function down(): void
    {
        Schema::table('project_book_sales', function (Blueprint $table) {
            $table->dropIndex('project_book_sales_book_date_index');
            $table->dropIndex('project_book_sales_date_index');
        });

        Schema::table('storage_distribution_costs', function (Blueprint $table) {
            $table->dropIndex('storage_distribution_costs_book_date_index');
            $table->dropIndex('storage_distribution_costs_date_index');
        });

        Schema::table('storage_payouts', function (Blueprint $table) {
            $table->dropIndex('storage_payouts_registration_year_quarter_index');
            $table->dropIndex('storage_payouts_year_quarter_index');
        });
    }
};
