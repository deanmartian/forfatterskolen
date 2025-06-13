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
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->timestamp('renewed_at')->nullable()->after('can_receive_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->removeColumn('renewed_at');
        });
    }
};
