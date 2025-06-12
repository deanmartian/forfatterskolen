<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'storage_distribution_costs',
        'storage_inventories',
        'storage_sales',
        'storage_various',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['user_book_for_sale_id']);
                $table->dropColumn('user_book_for_sale_id');
                $table->unsignedInteger('project_book_id')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedInteger('user_book_for_sale_id');
                $table->dropColumn('project_book_id');
            });
        }
    }
};
