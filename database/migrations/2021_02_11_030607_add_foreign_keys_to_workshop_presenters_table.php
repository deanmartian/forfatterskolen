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
        Schema::table('workshop_presenters', function (Blueprint $table) {
            $table->foreign('workshop_id', 'workshop_presenters_ibfk_1')->references('id')->on('workshops')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_presenters', function (Blueprint $table) {
            $table->dropForeign('workshop_presenters_ibfk_1');
        });
    }
};
