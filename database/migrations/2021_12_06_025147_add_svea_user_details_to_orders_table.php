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
            $table->string('svea_fullname')->nullable()->after('svea_payment_type_description');
            $table->string('svea_street')->nullable()->after('svea_fullname');
            $table->string('svea_postal_code')->nullable()->after('svea_street');
            $table->string('svea_city')->nullable()->after('svea_postal_code');
            $table->string('svea_country_code')->nullable()->after('svea_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('svea_fullname');
            $table->dropColumn('svea_street');
            $table->dropColumn('svea_postal_code');
            $table->dropColumn('svea_city');
            $table->dropColumn('svea_country_code');
        });
    }
};
