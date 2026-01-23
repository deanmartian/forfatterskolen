<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('author_payouts', function (Blueprint $table) {
            $table->string('statement_path')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('author_payouts', function (Blueprint $table) {
            $table->dropColumn('statement_path');
        });
    }
};
