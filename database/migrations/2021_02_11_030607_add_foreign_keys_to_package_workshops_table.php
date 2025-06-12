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
        Schema::table('package_workshops', function (Blueprint $table) {
            $table->foreign('package_id', 'package_workshops_ibfk_1')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('workshop_id', 'package_workshops_ibfk_2')->references('id')->on('workshops')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_workshops', function (Blueprint $table) {
            $table->dropForeign('package_workshops_ibfk_1');
            $table->dropForeign('package_workshops_ibfk_2');
        });
    }
};
