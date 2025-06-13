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
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('is_pay_later')->after('is_credited_amount');
        });

        Schema::table('courses_taken', function (Blueprint $table) {
            $table->tinyInteger('is_pay_later')->after('can_receive_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_pay_later');
        });

        Schema::table('courses_taken', function (Blueprint $table) {
            $table->dropColumn('is_pay_later');
        });
    }
};
