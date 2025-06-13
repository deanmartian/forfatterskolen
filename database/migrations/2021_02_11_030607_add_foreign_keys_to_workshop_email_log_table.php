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
        Schema::table('workshop_email_log', function (Blueprint $table) {
            $table->foreign('workshop_id', 'workshop_email_log_workshop_id')->references('id')->on('workshops')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_email_log', function (Blueprint $table) {
            $table->dropForeign('workshop_email_log_workshop_id');
        });
    }
};
