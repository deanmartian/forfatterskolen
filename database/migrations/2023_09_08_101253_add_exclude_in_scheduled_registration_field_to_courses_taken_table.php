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
            $table->tinyInteger('exclude_in_scheduled_registration')->default(0)->after('is_pay_later');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses_taken', function (Blueprint $table) {
            $table->dropColumn('exclude_in_scheduled_registration');
        });
    }
};
