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
        Schema::table('storage_details', function (Blueprint $table) {
            $table->dropForeign(['user_book_for_sale_id']);
            $table->dropColumn('user_book_for_sale_id');
            $table->unsignedInteger('project_book_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_details', function (Blueprint $table) {
            $table->unsignedInteger('user_book_for_sale_id');
            $table->dropColumn('project_book_id');
        });
    }
};
