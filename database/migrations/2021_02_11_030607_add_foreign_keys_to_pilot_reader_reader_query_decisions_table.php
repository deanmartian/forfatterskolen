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
        Schema::table('pilot_reader_reader_query_decisions', function (Blueprint $table) {
            $table->foreign('query_id', 'reader_query_decisions_query_id_foreign')->references('id')->on('pilot_reader_reader_queries')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilot_reader_reader_query_decisions', function (Blueprint $table) {
            $table->dropForeign('reader_query_decisions_query_id_foreign');
        });
    }
};
