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
        Schema::table('publishing_services', function (Blueprint $table) {
            $table->decimal('price', 11, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publishing_services', function (Blueprint $table) {
            $table->decimal('price', 11, 2)->change();
        });
    }
};
