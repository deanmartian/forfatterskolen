<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('name');
            $table->string('from')->nullable()->after('genre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('free_manuscripts', function (Blueprint $table) {
            $table->dropColumn('last_name');
            $table->dropColumn('from');
        });
    }
};
