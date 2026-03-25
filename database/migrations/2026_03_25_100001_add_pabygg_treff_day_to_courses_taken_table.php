<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->enum('pabygg_treff_day', ['friday', 'saturday'])->nullable()->after('in_facebook_group');
        });
    }

    public function down(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->dropColumn('pabygg_treff_day');
        });
    }
};
